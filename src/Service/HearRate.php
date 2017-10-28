<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class HearRate {

    protected $connection;

    /**
     * HearRate constructor.
     *
     * @param Connection $connection
     */
    public function __construct( Connection $connection ) {
        $this->connection = $connection;

        $this->connection->setFetchMode( \PDO::FETCH_NUM );
    }


    public function fetchRange( $from, $to ): array {
        $all = $this->connection->fetchAll(
            'SELECT time || \'000\', hr FROM heartrate WHERE time >= :from AND time < :to',
            [
                ':from' => $from,
                ':to'   => $to
            ] );

        $all = array_map( function ( $elm ) {
            return array_map( 'intval', $elm );
        }, $all );

        return $all;
    }


    public function fetchMin( $from, $to ) {
        return $this->connection->fetchColumn(
            'SELECT min(hr) FROM heartrate WHERE time >= :from AND time < :to',
            [
                ':from' => $from,
                ':to'   => $to
            ] );
    }

    public function fetchMax( $from, $to ) {
        return $this->connection->fetchColumn(
            'SELECT MAX(hr) FROM heartrate WHERE time >= :from AND time < :to',
            [
                ':from' => $from,
                ':to'   => $to
            ] );
    }

    public function fetchAvg( $from, $to ) {
        return round( $this->connection->fetchColumn(
            'SELECT AVG(hr) FROM heartrate WHERE time >= :from AND time < :to',
            [
                ':from' => $from,
                ':to'   => $to
            ] ) );
    }
}