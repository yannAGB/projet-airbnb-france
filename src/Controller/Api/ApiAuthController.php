<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Service\UserService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/api', name: 'api_auth_')]
final class ApiAuthController extends AbstractController
{
    public function __construct(
        private UserService   $userService,
        private EmailVerifier $emailVerifier,
    ) {}

    /* -------------------------------------------------- */
    /*              POST /api/register                    */
    /* -------------------------------------------------- */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $donnees = json_decode($request->getContent(), true);

        /* Validation */
        $validation = $this->userService->validerDonnees($donnees);
        if (!$validation['success']) {
            return $this->json($validation, Response::HTTP_BAD_REQUEST);
        }

        /* Unicité email */
        if ($this->userService->emailExiste($donnees['email'])) {
            return $this->json([
                'success' => false,
                'message' => 'Cet email est déjà utilisé',
            ], Response::HTTP_CONFLICT);
        }

        /* Unicité username */
        if ($this->userService->usernameExiste($donnees['username'])) {
            return $this->json([
                'success' => false,
                'message' => 'Ce nom d\'utilisateur est déjà pris',
            ], Response::HTTP_CONFLICT);
        }

        /* Création */
        try {
            $user = $this->userService->creerUtilisateur($donnees);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erreur lors de la création du compte',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        /* Envoi email de vérification */
        $this->emailVerifier->sendEmailConfirmation(
            'api_auth_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@trouvezMoi.com', 'TrouvezMoi'))
                ->to((string) $user->getEmail())
                ->subject('Confirmez votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->json([
            'success' => true,
            'message' => 'Inscription réussie. Vérifiez votre email pour activer votre compte.',
            'data'    => $this->userService->serialiser($user),
        ], Response::HTTP_CREATED);
    }

    /* -------------------------------------------------- */
    /*              GET /api/verify/email                 */
    /* -------------------------------------------------- */
    #[Route('/verify/email', name: 'verify_email', methods: ['GET'])]
    public function verifyEmail(
        Request             $request,
        TranslatorInterface $translator
    ): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $e) {
            return $this->json([
                'success' => false,
                'message' => $translator->trans(
                    $e->getReason(), [], 'VerifyEmailBundle'
                ),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'success' => true,
            'message' => 'Email vérifié avec succès. Votre compte est maintenant actif.',
        ], Response::HTTP_OK);
    }

    /* -------------------------------------------------- */
    /*         GET /api/me — utilisateur connecté         */
    /* -------------------------------------------------- */
    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'success' => true,
            'data'    => $this->userService->serialiser($user),
        ], Response::HTTP_OK);
    }
}