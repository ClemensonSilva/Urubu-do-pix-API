<?php
namespace App\Controllers;

use App\Models\TransactionModel;

class TransactionController
{
    public function createTransaction($transactionParams)
    {
        $transaction = new TransactionModel();
        echo json_encode($transaction->createTransaction($transactionParams));
    }
    public static function profitInvestiment($transactionParams)
    {
        echo json_encode(
            TransactionModel::profitInvestiment($transactionParams)
        );
    }
}
?>
