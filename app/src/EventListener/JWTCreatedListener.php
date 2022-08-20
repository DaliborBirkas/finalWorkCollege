<?php
namespace App\EventListener;

use App\Entity\Logs;
use App\Entity\User;
use Browser;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, private readonly EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload= $event->getData();
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$payload['email']]);

        $browser = new Browser();
        date_default_timezone_set("Europe/Belgrade");
        $date = new \DateTime();
        $browserName = $browser->getBrowser();
        $browserPlatform = $browser->getPlatform();
        $browserDevice = "Desktop";
        if ($browser->isMobile()){
            $browserDevice = "Mobile";
        }if ($browser->isTablet()){
            $browserName = "Table";
        }
        $log = new Logs();
        $log->setUser($user);
        $log->setBrowser($browserName);
        $log->setPlatform($browserPlatform);
        $log->setDevice($browserDevice);
        $log->setLogTime($date);

        $this->em->persist($log);
        $this->em->flush();

        $payload['ip'] = $request->getClientIp();
        $payload['name'] = $user->getName();
        $payload['surname'] = $user->getSurname();
        $payload['companyName'] = $user->getCompanyName();

        $header        = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setData($payload);
        $event->setHeader($header);
    }
}