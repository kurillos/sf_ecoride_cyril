<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class RegistrationFormTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'pseudo' => 'johndoe',
            'email' => 'john.doe@example.com',
            'plainPassword' => [
                'first' => 'Password123!',
                'second' => 'Password123!',
            ],
            'agreeTerms' => true,
        ];

        $model = new User();
        $form = $this->factory->create(RegistrationFormType::class, $model);

        $expected = new User();
        $expected->setFirstName('John');
        $expected->setLastName('Doe');
        $expected->setPseudo('johndoe');
        $expected->setEmail('john.doe@example.com');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected->getFirstName(), $model->getFirstName());
        $this->assertEquals($expected->getLastName(), $model->getLastName());
        $this->assertEquals($expected->getPseudo(), $model->getPseudo());
        $this->assertEquals($expected->getEmail(), $model->getEmail());
    }

    public function testSubmitInvalidPassword(): void
    {
        $formData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'pseudo' => 'johndoe',
            'email' => 'john.doe@example.com',
            'plainPassword' => [
                'first' => 'weak',
                'second' => 'weak',
            ],
            'agreeTerms' => true,
        ];

        $model = new User();
        $form = $this->factory->create(RegistrationFormType::class, $model);

        $form->submit($formData);

        $this->assertFalse($form->isValid());
        $this->assertCount(1, $form->get('plainPassword')->get('first')->getErrors());
    }
}