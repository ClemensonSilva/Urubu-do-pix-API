<?php
namespace App\Controllers;

use App\Database\Databases;
use App\Models\TransactionModel;
require_once "../src/Database/Pdo.php"; // isso é temporario ate ajustar os namespaces completamente

class TransactionController
{
    public function createTransaction($transactionParams)
    {
        if (
            empty($transactionParams->depositValue) ||
            empty($transactionParams->user_id)
        ) {
            echo json_encode(
                Databases::genericMessage(
                    "error",
                    "The transaction data like depositValue and user id is mandatory."
                )
            );
            return 0;
        }

        $transaction = new TransactionModel();
        echo json_encode($transaction->createTransaction($transactionParams));
    }
    public function withdraw($transactionParams)
    {
        if (
            empty($transactionParams->transaction_id) ||
            empty($transactionParams->user_id) ||
            empty($transactionParams->valueToWithdraw)
        ) {
            echo json_encode(
                Databases::genericMessage(
                    "error",
                    "The transaction data like transaction_id, user id and valueToWithdraw is mandatory."
                )
            );
            return 0;
        }
        echo json_encode(TransactionModel::withdraw($transactionParams));
    }
    public static function profitInvestiment($transactionParams)
    {
        if (
            empty($transactionParams->transaction_id) ||
            empty($transactionParams->user_id)
        ) {
            echo json_encode(
                Databases::genericMessage(
                    "error",
                    "The transaction data like transaction_id and user id is mandatory."
                )
            );
            return 0;
        }
        $result = TransactionModel::profitInvestiment($transactionParams);
        if (is_string($result)) {
            echo $result;
        } else {
            echo json_encode($result);
        }
    }
}
?>
