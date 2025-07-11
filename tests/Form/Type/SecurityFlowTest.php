<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityFlowTest extends WebTestCase 
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->passwordHasher = $kernel->getContainer()->get('security.user_passord_hasher');

        $this->entityManager->getConnection()->executeStatement('DELETE FROM user');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @return User L'utilisateur de test créé
     */
    private function createTestUser(string $email, string $password, array $roles = ['ROLE_USER']): User
    {
        $user= new User();
        $user->setEmail($email);
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setPseudo('testUser');

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $user->setRoles($roles);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function testLoginSuccessful(): void
    {
        $this->createTestUser('test@example.com', 'Password123!');

        $crawler = $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test@example.com',
            '_password' => 'Password123!',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects('/');
        $crawler = $this->client->followRedicrect();

        self::assertSelectorTextContains('nav', 'Bienvenue, testuser');
    }

    public function testLoginWithBadCredentials(): void
    {
        $this->createTestUser('test@example.com', 'Password123!');

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test@example.com',
            '_password' => 'wrong_password',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects('/login');
        $crawler = $this->client->followRedirect();
        self::assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials');
    }

    public function testLogout(): void
    {
        $user = $this->createTestUser('logout@example.com', 'Password123!');
        $this->client->loginUser($user); // Connecte un utillisateur de test

        $this->client->request('GET', '/profile');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextNotContains('body', 'logout@example.com');

        $this->client->request('GET', '/logout');
        self::assertResponseRedirects('/');
        $wrawler = $this->client->followRedirect();

        self::assertSelectorTextContains('nav', 'Connexion');
    }

    // Test avec différents rôles
    public function testLoginAsDriver(): Void
    {
        $this->createTestUser('driver@example.com', 'Password123!', ['ROLE_DRIVER']);

        $this->client->request('GET', '/login');
        $form = $this->client->getCrawler()->selectButton('Se connecter')->form([
            '_username' => 'driver@example.com',
            '_password' => 'Password123!',
        ]);

        $this->client->submit($form);
        self::assertResponseRedirects('/');
        $crawler = $this->client->followRedirect();
        // self::assertSelector
    }

    public function testLoginAsPassenger(): void
    {
        $this->createTestUser('passenger@exmpale.com', 'Password123!', ['ROLE_PASSENGER']);
        $this->client->request('GET', '/login');
        $form = $this->client->getCrawler()->selectButton('Se connecter')->form([
            '_username' => 'passenger@example.com',
            '_password' => 'Password123!',
        ]);

        $this->client->submit($form);
        self::assertResponseRedirects('/');
        $crawler = $this->client->followRedirect();
        // self::assert
    }
}