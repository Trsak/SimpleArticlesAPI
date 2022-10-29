<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class BaseController extends AbstractController
{
    /**
     * @return string[]
     */
    protected function getValidationErrorsArray(ConstraintViolationListInterface $constraintViolationList): array
    {
        $errors = [];

        foreach ($constraintViolationList as $violation) {
            $errors[] = $violation->getPropertyPath() . ' : ' . $violation->getMessage();
        }

        return $errors;
    }
}
