<?php

class Dashboard extends Controller
{
    public function index()
    {
        $this->requireLogin();

        $transactionModel = $this->model('Transaction');
        $productModel = $this->model('Product');
        $purchaseModel = $this->model('Purchase');

        $data = [
            'title'              => 'Dashboard',
            'todaySales'         => $transactionModel->getTodaySales(),
            'todayTransactions'  => $transactionModel->getTodayCount(),
            'monthlyTotal'       => $transactionModel->getMonthlyTotal(),
            'monthlyPurchases'   => $purchaseModel->getMonthlyTotal(),
            'totalProducts'      => $productModel->countAll(),
            'lowStockProducts'   => $productModel->getLowStock(10),
            'recentTransactions' => $transactionModel->getRecent(5),
            'dailySales'         => $transactionModel->getDailySales(7)
        ];

        $this->view('dashboard/index', $data);
    }
}
