<?php

namespace App\Controller;


use App\Service\HearRate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HeartRateController
 * @package App\Controller
 *
 * @Route("/heartrate")
 */
class HeartRateController {

    /**
     * @param HearRate $hr
     * @param $from
     * @param $to
     *
     * @return Response
     *
     * @Route("/data/{from}/{to}")
     */
    public function dataAction( HearRate $hr, $from, $to ): Response {
        return new JsonResponse( $hr->fetchRange( $from, $to ) );
    }

    /**
     * @param HearRate $hr
     *
     * @param $from
     * @param $to
     *
     * @return Response
     *
     * @Route("/aggregates/{from}/{to}")
     */
    public function aggregatesAction( HearRate $hr, $from, $to ): Response {
        $min = $hr->fetchMin( $from, $to ) ?? 0;
        $max = $hr->fetchMax( $from, $to ) ?? 0;
        $avg = $hr->fetchAvg( $from, $to ) ?? 0;

        return new JsonResponse( compact( 'min', 'max', 'avg' ) );
    }
}