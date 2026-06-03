<?php

function sumByDevise($pdo, $table, $column, $devise)
{
    $sql = "
        SELECT COALESCE(SUM($column),0)
        FROM $table
        WHERE devise = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$devise]);

    return $stmt->fetchColumn();
}

// code ex : 
//  $total_sociale_usd = sumByDevise($pdo,'cultes','sociale','USD');
//                                 $total_sociale_cdf = sumByDevise($pdo,'cultes','sociale','CDF');