<?php

namespace App\Controller\Api\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Api\ApiErrorsTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\FOSRestBundle;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @author Kevin Mougammadaly <kevin.mougammadaly@ekino.com>
 *
 */
class UserController extends FOSRestBundle
{
    use ApiErrorsTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $doctrine;

    public function __construct(
        UserRepository $userRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $doctrine
    )
    {
        $this->userRepository = $userRepository;
        $this->formFactory = $formFactory;
        $this->doctrine = $doctrine;
    }

    /**
     * @Rest\Get("users")
     * @Rest\View(statusCode=Response::HTTP_OK)
     *
     */
    public function getAll()
    {
        return $this->userRepository->findAll();
    }
}
