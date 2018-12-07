<?php
namespace App\Controller;
use App\Controller\Api\ApiErrorsTrait;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\FOSRestBundle;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\SerializerInterface;

class AuthController extends FOSRestBundle
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

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        UserRepository $userRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $doctrine,
        UserPasswordEncoderInterface $encoder,
        SerializerInterface $serializer
    )
    {
        $this->userRepository = $userRepository;
        $this->formFactory    = $formFactory;
        $this->doctrine       = $doctrine;
        $this->encoder        = $encoder;
        $this->serializer     = $serializer;
    }

    public function register(Request $request)
    {
        $user = new User();
        $user->setRoles([User::ROLE_USER]);

        return $this->handleUser($user, $request);
    }

    private function handleUser(User $user, Request $request, bool $clearMissing = true)
    {
        $form = $this->formFactory->create(UserType::class, $user);
        $form->submit($request->request->all(), $clearMissing);

        if (!$form->isValid()) {
            $readableErrors = $this->getFormErrors($form);

            return new JsonResponse(
                ['message' => 'Invalid data sent', 'errors' => $readableErrors],
                Response::HTTP_UNPROCESSABLE_ENTITY)
                ;
        }

        $this->encodePassword($user);
        $this->doctrine->persist($user);
        $this->doctrine->flush();

        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }

    private function encodePassword(User $user): void
    {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
    }
}
