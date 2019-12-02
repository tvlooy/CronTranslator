<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Intracto\Cron\CronExpression;

$request = Request::createFromGlobals();
$twig = new Environment(new FilesystemLoader(__DIR__ . '/../templates'));

if ($request->isMethod(Request::METHOD_GET)) {
    echo $twig->render('index.twig', [
        'cron' => '*/15 8-16,03 * * *',
    ]);
}

if ($request->isMethod(Request::METHOD_POST)) {
    try {
        $cron = CronExpression::factory($request->get('cron'));

        $nextRuns = 'Next runs  : ';
        foreach ($cron->getMultipleRunDates(10) as $nextRun) {
            $nextRuns .= $nextRun->format('d/m/Y H:i:s') .
                '<br>             ';
        }

        echo $twig->render('index.twig', [
            'cron' => (string) $cron,
            'body' => 'Will run   :<br>' .
                $cron->toEnglish() . '<br>' .
                'Last run   : ' . $cron->getPreviousRunDate()->format('d/m/Y H:i:s') . '<br>' .
                $nextRuns
        ]);
    } catch (\Exception $e) {
        echo $twig->render('index.twig', [
            'cron' => '*/15 8-16,03 * * *',
        ]);
    }
}
