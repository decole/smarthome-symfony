<?php


namespace App\Application\Http\Web\Auth;


use App\Application\Service\SignUpService;
use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\SecureSystem\Dto\RegisterDto;
use App\Domain\Doctrine\SecureSystem\Service\RegistrationValidateService;
use App\Infrastructure\Security\Auth\EmailVerifier;
use App\Infrastructure\Security\Auth\Service\CsrfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Webmozart\Assert\InvalidArgumentException;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private CsrfService $csrf,
        private RegistrationValidateService $validation,
        private SignUpService $service,
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $dto = new RegisterDto($request);

        [$valid, $errors] = $this->validation->validate($dto, $this->csrf->getToken());

        if (!$valid) {
            $this->addFlash('errors', $errors);

            return $this->redirectToRoute('app_signup');
        }

        $this->service->sugnUp($dto);

        return $this->redirectToRoute('home');
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, EntityManagerInterface $manager): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $repository = $manager->getRepository(User::class);
        $user = $repository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        } catch (InvalidArgumentException $e) {
            $this->addFlash('errors', [
                'user_confirm' => 'user has confirmed',
                'email_verify' => $e->getMessage()
            ]);

            return $this->redirectToRoute('app_signup');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('home');
    }
}
