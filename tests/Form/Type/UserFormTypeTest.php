<?php 

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class RegistrationFormTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
            $validator = Validation::createValidatorBuilder()
                ->enableAnnotationMapping()
                ->addDefaultDoctrineMapping()
                ->getValidator();
            return [
                new ValidatorExtension($validator),
            ];

        }

    public function testSubmitValidData()
    {
        $formData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'plainPassword' => [
                'first' => 'SecurePass123!',
                'second' => 'SecurePass123!',
            ],
            'agreeTerms' => true,
        ];

        $user = new User();
        // $user->setRoles(['ROLE_USER]);

        $form = $this->factory->create(RegistrationFormType::class, $user);

        $form = $this->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertEquals('johndoe', $user->getPseudo());
        $this->assertEquals('john.doe@example.com', $user->getEmail());
        $this->assertTrue($user->isAgreeTerms());

        $this->assertNotEmpty($form->get('plainPassword')->getErrors(true));
    }

    public function testSubmitinvalidEmail()
    {
        $formData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'pseudo' => 'janedoe',
            'email' => 'invalid-email',
            'plainPassword' => [
                'first' => 'Securepass123!',
                'second' => 'SecurePass123!',
            ],
            'agreeTerms' => true,
        ];

        $form = $this->factory->create(RegistrationFormType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid());
        $this->assertFalse($form->get('email')->isValid());

        $this->assertCount(1, $form->get('email')->getErrors());
        $this->assertEquals("Cette valeur n'est pas une adresse mail valide.", $form->get('email')->getErrors(true)[0]->getMessage());
    }

    public function testSubmitPasswordMismatch()
    {
        $formData = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'pseudo' => 'testuser',
            'email' => 'test@example.com',
            'plainPassword' => [
                'first' => 'Pass123!',
                'second' => 'Dofferent',
            ],
            'agreeTerms' => true,
        ];

        $form = $this->factory->create(RegistrationFormType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid());
        $this->assertFalse($form->get('plainPassword')->isValid());
        $this->assertCount(1, $form->get('plainPassword')->getErrors(true));
        $this->assertEquals('Les mots de passe ne correspondent pas.', $form->get('plainPassword')->getErrors(true)[0]->getMessage());
    }

}