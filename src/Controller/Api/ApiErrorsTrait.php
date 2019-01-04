<?php

/*
 * This file is part of the Skoda project.
 *
 * (c) Skoda
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Api;

use Symfony\Component\Form\FormInterface;

/**
 * Trait ApiErrorsTrait.
 *
 * @author Kevin Mougammadaly <kevin.mougammadaly@gmail.com>
 */
trait ApiErrorsTrait
{
    private function getFormErrors(FormInterface $form): string
    {
        $readableErrors = [];

        foreach ($form->getErrors(true) as $error) {
            $readableErrors[] = sprintf(
                '[%s] %s : %s',
                $error->getCause()->getPropertyPath(),
                $error->getMessage(),
                \is_array($error->getCause()->getInvalidValue()) ? implode(', ', $error->getCause()->getInvalidValue()) : $error->getCause()->getInvalidValue()
            );
        }

        return implode(', ', $readableErrors);
    }
}
