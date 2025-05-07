<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display the store homepage.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get products that are in stock
        $availableProducts = Product::where('stock_quantity', '>', 0)
                                   ->with('category')
                                   ->latest()
                                   ->get();
        
        return view('store.index', [
            'title' => __('app.store_welcome'),
            'availableProducts' => $availableProducts,
        ]);
    }

    /**
     * Display the about us page.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('store.about', [
            'title' => __('app.about_us'),
        ]);
    }

    /**
     * Display the contact page.
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('store.contact', [
            'title' => __('app.contact_us'),
        ]);
    }

    /**
     * Process contact form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Process contact form submission
        // This is where you would send emails, store in database, etc.
        
        return redirect()->route('store.contact')
            ->with('success', __('app.message_sent_successfully'));
    }
}