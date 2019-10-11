<?php

use Swoole\Timer;
use Swoole\Coroutine as Co;
use Swoole\Coroutine\Channel;

const LEVEL_LOW = false;
const LEVEL_HIGH = true;

$ch = new Channel(10);

// Clock
$timer = Timer::tick(500, function () use ($ch) {
    $ch->push(['t' => 'invert']);
});

Timer::after(11000, function () use ($timer) {
    Timer::clear($timer);
});

// Input Terminal
Timer::after(1500, function () use ($ch) {
    $ch->push([
        'j' => LEVEL_HIGH,
        'k' => LEVEL_LOW,
    ]);
});
Timer::after(2500, function () use ($ch) {
    $ch->push([
        'j' => LEVEL_LOW,
        'k' => LEVEL_LOW,
    ]);
});
Timer::after(3500, function () use ($ch) {
    $ch->push([
        'j' => LEVEL_LOW,
        'k' => LEVEL_HIGH,
    ]);
});
Timer::after(4500, function () use ($ch) {
    $ch->push([
        'j' => LEVEL_LOW,
        'k' => LEVEL_LOW,
    ]);
});
Timer::after(5500, function () use ($ch) {
    $ch->push([
        'j' => LEVEL_HIGH,
        'k' => LEVEL_HIGH,
    ]);
});
Timer::after(9500, function () use ($ch) {
    $ch->push([
        'j' => LEVEL_LOW,
        'k' => LEVEL_LOW,
    ]);
});

Co::create(function () use ($ch) {
    // Initial Condition
    $tickCount = 0;
    $t = LEVEL_LOW;
    $j = LEVEL_LOW;
    $k = LEVEL_LOW;
    $q = LEVEL_LOW;

    printf(" step\t| T\t  J\t  K\t| Q\n");
    printf("--------+-----------------------+-------\n");
    printf(" %d\t| %d\t  %d\t  %d\t| %d\n", $tickCount, $t, $j, $k, $q);
    while ($tickCount < 20) {
        $input = $ch->pop();

        if (isset($input['j'])) {
            $j = $input['j'];
        }

        if (isset($input['k'])) {
            $k = $input['k'];
        }

        if (isset($input['t']) && $input['t'] === 'invert') {
            $t = !$t;
            $tickCount++;

            // rising edge
            if ($t === LEVEL_HIGH) {
                if ($j === LEVEL_HIGH && $k === LEVEL_HIGH) {
                    $q = !$q;
                }

                if ($j === LEVEL_HIGH && $k === LEVEL_LOW) {
                    $q = LEVEL_HIGH;
                }

                if ($j === LEVEL_LOW && $k === LEVEL_HIGH) {
                    $q = LEVEL_LOW;
                }

                if ($j === LEVEL_LOW && $k === LEVEL_LOW) {
                    $q = $q;
                }
            }

            printf(" %d\t| %d\t  %d\t  %d\t| %d\n", $tickCount, $t, $j, $k, $q);
        }
    }
});
