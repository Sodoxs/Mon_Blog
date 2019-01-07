<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\RequestStack;

class Verification
{

    //private $tabString = array("aaaaa", "sdfsdf");
    private $tabString;
    private $logger;
    private $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack, $tabString)
    {
        $this->tabString = $tabString;
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function Spam($myStringTest)
    {
//        if($myStringTest == 'aaaaa' || $myStringTest == 'sdfsdf')
//        {
//            $res = true;
//        }
//        else
//        {
//            $res = false;
//        }
//        return $res;
//
//        $res = false;
//        dump($this->tabString, $myStringTest);

        foreach($this->tabString as $test)
        {
            //dump($myStringTest, $test, strpos($myStringTest, $test));
            if((strpos($myStringTest, $test) !== false))
            {
                $this->requestStack->getCurrentRequest()->getClientIp();
                $this->logger->info('Spam detected');
                return true;
            }
        }
        return false;
    }
}