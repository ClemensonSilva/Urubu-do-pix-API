<?php
namespace App\Controllers;

use App\Models\TransactionModel;

class TransactionController
{
    public function createTransaction($transactionParams)
    {
        $transaction = new TransactionModel();
        $transaction->createTransaction($transactionParams);
    }
    public function profitInvestiment($transactionParams)
    {
        $transaction = new TransactionModel();
        return $transaction->profitInvestiment($transactionParams);
    }
}
?>
