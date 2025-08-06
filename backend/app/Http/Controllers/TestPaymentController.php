<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Skilleton\PaymentPackage\Dto\BuyerDto;
use Skilleton\PaymentPackage\Dto\CreatePaymentDto;
use Skilleton\PaymentPackage\Dto\ProductDto;
use Skilleton\PaymentPackage\Facades\Payment;

class TestPaymentController extends Controller
{
    public function showPaymentForm()
    {
        return view('test_payment');
    }

    public function createPayment(Request $request)
    {
        $buyer = new BuyerDto(
            email: 'test@example.com',
            firstName: 'Jan',
            lastName: 'Kowalski',
            phone: '123456789'
        );

        $products = [
            new ProductDto(
                name: 'Test Product',
                unitPrice: 100.00,
                quantity: 1,
                category: 'Test'
            )
        ];

        $paymentDto = new CreatePaymentDto(
            amount: 100.00,
            channel: $request->input('channel'),
            currency: 'PLN',
            externalId: 'test_order_' . time(),
            description: 'Test Payment',
            returnUrl: route('p24.status'),
            country: 'PL',
            language: 'pl',
            buyer: $buyer,
            products: $products
        );

        $paymentResponse = Payment::create($paymentDto);

        return redirect($paymentResponse->getPaymentUrl());
    }
}
