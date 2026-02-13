<?php

class Dashboard extends Controller
{
    public function index()
    {
        $transactionModel = $this->model('Transaction');
        
        $data = [
            'title' => 'Dashboard',
            'todaySales' => $transactionModel->getTodaySales(),
            'todayTransactions' => $transactionModel->getTodayCount(),
            'recentTransactions' => $transactionModel->getRecent(10)
        ];

        $this->view('dashboard/index', $data);
    }
}
