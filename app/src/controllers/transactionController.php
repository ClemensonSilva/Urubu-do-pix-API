<?php
    namespace App\controllers;

    use App\models\TransactionModel;
    
    class TransactionController
    {
        public function createTransaction($transactionParams){
            $transaction = new TransactionModel();
            $transaction->createTransaction($transactionParams);
        }
        public function profitInvestiment($transactionParams){
            $transaction = new TransactionModel();
            return $transaction->profitInvestiment($transactionParams);
        }
    }
?>