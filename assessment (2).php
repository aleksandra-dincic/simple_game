<?php

$players = ['Otto', 'John', 'Jane', 'Jan'];

$win['Otto'] = 0;
$win['John'] = 0;
$win['Jane'] = 0;
$win['Jan'] = 0;

playGame($players, $win);

function playGame($players, $win) {
    $data['Otto'] = shuffleCards();
    $data['John'] = shuffleCards();
    $data['Jane'] = shuffleCards();
    $data['Jan'] = shuffleCards();
    foreach ($data as $key => $value)
        echo $key . ' has been dealt: ' . implode(', ', $data[$key]) . "\n";

    $result = -1;
    for($i = 0; $i < 8; $i++) {
        $cardsInRound = array();
        $tmp = $players;

        $firstPlayer = playFirstInRound($tmp, $data);
        print "\nRound " . $i . ' '. $firstPlayer['activePlayer'] . " started a game.\n";
        print $firstPlayer['activePlayer'] . ' played ' . $firstPlayer['activeCard'] . "\n";

        $cardsInRound[$firstPlayer['activePlayer']] =  $firstPlayer['preparedCard'];
        $data[ $firstPlayer['activePlayer']] = $firstPlayer['cards'];
        $keyPlayer = $firstPlayer['keyPlayer'];

        for($j = 0; $j < 3; $j++) {
            if($keyPlayer == 3)
                $keyPlayer = 0;
            else
                $keyPlayer++;

            $activePlayer = $players[$keyPlayer];

            $cardArray = playMinimalOrRandomCard($cardsInRound, $data[$activePlayer]);
            $cardsInRound[$activePlayer] = $cardArray['preparedCard'];
            print $activePlayer . ' played ' . $cardArray['activeCard'] . "\n";
        }

        $score = matchScore($win, $cardsInRound);
        $win = $score['winArray'];

        print $score['winner'] . ' played '.$cardsInRound[$score['winner']].', the highest matching card of this match and got 1 point added to his total
score. '. $score['winner']."’s total score is ".$win[$score['winner']]." point.\n";

        $result = chechResualt($win);
        if($result != -1) {
            print $result." loses the game! Points: \n";
            foreach ($win as $key => $value) {
                print $key.":" . $value ." \n";
            }
            break;
        }
    }

    if($result == -1) {
        print "Players ran out of cards. Reshuffle \n";
        playGame($players, $win);
    }
}

function shuffleCards() {
    $suits = ['clubs', 'hearts', 'spades', 'diamonds'];
    $data = array();
    $i = 0;
    while ($i < 8) {
        $tmp = $suits[array_rand($suits, 1)] . ' ' . rand(7, 14);
        if(!array_search($tmp, $data)) {
            $data[$i] = $tmp;
            $i++;
        }
    }
    return $data;
}

function playFirstInRound($players, $cards) {
    $data = [];
    $keyPlayer = array_rand($players, 1);
    $data['activePlayer'] = $players[$keyPlayer];
    $data['keyPlayer'] = $keyPlayer;
    $cards = $cards[$players[$keyPlayer]];

    $keyCard = array_rand($cards, 1);

    $data['activeCard'] = $cards[$keyCard];
    $data['preparedCard'] = prepareCard($cards[$keyCard]);
    unset($cards[$keyCard]);
    $data['cards'] = $cards;

    return $data;
}

function prepareCard($card) {
    return substr($card, (strpos($card, ' ') ?: -1) + 1);
}

function playMinimalOrRandomCard($cardsInRound, $cards) {
    $data = array();
    $min = $cardsInRound[array_key_first($cardsInRound)];
    $returnCard = -1;
    foreach ($cards as $card) {
        if(prepareCard($card) < $min)
            $returnCard = $card;
    }

    if($returnCard == -1) {
        $keyCard = array_rand($cards, 1);
        $data['activeCard'] = $cards[$keyCard];
    } else {
        $keyCard = array_search($returnCard, $cards);
        $data['activeCard'] = $returnCard;
    }

    $data['preparedCard'] = prepareCard($cards[$keyCard]);
    unset($cards[$keyCard]);
    $data['cards'] = $cards;

    return $data;
}

function matchScore($winArray, $cardInRound) {

    $max = max($cardInRound);
    $keyCard = array_search($max, $cardInRound);

    $winArray[$keyCard] = $winArray[$keyCard] + 1;

    $min = min($cardInRound);

    $keyMin = array_search($min, $cardInRound);

    $winArray[$keyMin] = $winArray[$keyMin] + array_sum($cardInRound);

    return array(
        'winner' => $keyCard,
        'winArray' => $winArray,
    );
}

function chechResualt($cardsInRound) {

    foreach ($cardsInRound as $key => $value) {
        if($value >= 50)
            return $key;
    }

    return -1;
}

?>

