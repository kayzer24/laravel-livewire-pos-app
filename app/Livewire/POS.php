<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SalesItem;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class POS extends Component
{
    public $currency;

    public $tax_rate;

    public $items;

    public $customers;

    public $paymentMethods;

    public $search = '';

    public $cart = [];

    // Checkout properties
    public $customer_id = null;

    public $payment_method_id = null;

    public $paid_amount = 0;

    public $discount_amount = 0; // flat discount amount

    public function mount()
    {
        // load all items
        $this->items = Item::whereHas('inventory', function ($query) {
            $query->where('quantity', '>', '0');
        })
            ->with('inventory')
            ->where('status', 'active')
            ->get();

        // load all customers
        $this->customers = Customer::all();

        // load all PMs
        $this->paymentMethods = PaymentMethod::all();

        $this->currency = Config::get('app.currency');
        $this->tax_rate = Config::get('app.tax_rate');
    }

    #[Computed]
    public function filteredItems()
    {
        if (empty($this->search)) {
            return $this->items;
        }

        return $this->items->filter(function ($item) {
            return str_contains(strtolower($item->name), strtolower($this->search))
                || str_contains(strtolower($item->sku), strtolower($this->search));
        });
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    #[Computed]
    public function tax()
    {
        return $this->subtotal() * ($this->tax_rate / 100); // 20% vat
    }

    #[Computed]
    public function totalBeforeDiscount()
    {
        return $this->subtotal() + ($this->tax());
    }

    #[Computed]
    public function total()
    {
        return $this->totalBeforeDiscount() - $this->discount_amount;
    }

    #[Computed]
    public function change()
    {
        if ($this->paid_amount > $this->total()) {
            return $this->paid_amount - $this->total();
        }

        return 0;
    }

    public function render(): View
    {
        return view('components.p-o-s');
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Cart Empty!')
                ->body('Error processing. Cart empty')
                ->danger()
                ->send();

            return;
        }

        if ($this->paid_amount < $this->total()) {
            Notification::make()
                ->title('Cart Empty!')
                ->body('Paid amount is less than total!')
                ->danger()
                ->send();

            return;
        }

        // create the sale ... db transaction
        try {
            DB::beginTransaction();

            // create sale
            $sale = Sale::create([
                'total' => $this->total,
                'customer_id' => $this->customer_id,
                'payment_method_id' => $this->payment_method_id,
                'paid_amount' => $this->paid_amount,
                'discount' => $this->discount_amount,
            ]);

            // create the sale items
            foreach ($this->cart as $item) {
                SalesItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // update inventory
                $inventory = Inventory::where('item_id', $item['id'])->first();
                if ($inventory) {
                    $inventory->quantity -= $item['quantity'];
                    $inventory->save();
                }
            }

            DB::commit();

            // reset cart
            $this->cart = [];

            // Reset other properties
            $this->search = '';
            $this->customer_id = null;
            $this->payment_method_id = null;
            $this->paid_amount = 0;
            $this->discount_amount = 0;

            Notification::make()
                ->title('Success Sale!')
                ->body('Sale was made successfully')
                ->success()
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Failed Sale!')
                ->body('Failed to complete the sale, try again.')
                ->danger()
                ->send();
        }

    }

    public function addToCart(Item $item): void
    {
        // Inventory
        $inventory = $item->inventory()->first();

        if (! $inventory || $inventory->quantity <= 0) {
            Notification::make()
                ->title('This item is currently out of stock!')
                ->danger()
                ->send();

            return;
        }

        if (isset($this->cart[$item->id])) {
            $currentQuantity = $this->cart[$item->id]['quantity'];

            if ($currentQuantity >= $inventory->quantity) {
                Notification::make()
                    ->title("Cannot add more. Onle {$inventory->quantity} in stock")
                    ->danger()
                    ->send();

                return;
            }
            // add more items
            $this->cart[$item->id]['quantity']++;
        } else {
            $this->cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => $item->price,
                'quantity' => 1,
            ];
        }
    }

    public function removeFromCart(string $itemId): void
    {
        unset($this->cart[$itemId]);
    }

    public function updateQuantity($itemId, $quantity)
    {
        // ensure the quantity of an item is not less than 1
        $quantity = max(1, (int) $quantity);

        $inventory = Inventory::where('item_id', $itemId)->first();

        if ($quantity > $inventory->quantity) {
            Notification::make()
                ->title('Cannot add more. Only {$inventory->quantity} in stock')
                ->danger()
                ->send();

            $this->cart[$itemId]['quantity'] = $inventory->quantity;
        } else {
            $this->cart[$itemId]['quantity'] = $quantity;
        }
    }
}
