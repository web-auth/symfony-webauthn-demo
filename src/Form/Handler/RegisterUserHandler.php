<?php

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Handler;

use App\Form\Data\RegisterUser;
use App\Form\Type\RegisterUserType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterUserHandler
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function prepare(RegisterUser $data): FormInterface
    {
        return $this->formFactory->create(RegisterUserType::class, $data);
    }

    public function process(Request $request, FormInterface $form): bool
    {
        $form->handleRequest($request);

        return $form->isSubmitted() && $form->isValid();
    }
}
