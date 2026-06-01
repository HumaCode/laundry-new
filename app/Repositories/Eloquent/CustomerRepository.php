<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * Get paginated customers with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage)
    {
        $query = User::role('customer')->with('outlet');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['tier'])) {
            $query->where('tier', $filters['tier']);
        }

        if (!empty($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        }

        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Find customer by ID.
     */
    public function findById(string $id)
    {
        return User::role('customer')->with('outlet')->findOrFail($id);
    }

    /**
     * Create a new customer.
     */
    public function create(array $data)
    {
        // Default password for customers if not provided
        if (empty($data['password'])) {
            $data['password'] = Hash::make('123');
        }

        // Generate username from email or name if not provided
        if (empty($data['username'])) {
            $data['username'] = Str::slug($data['name']) . '-' . Str::random(4);
        }

        // Default email if not provided
        if (empty($data['email'])) {
            $data['email'] = Str::slug($data['name']) . '-' . Str::random(4) . '@email.com';
        }

        $user = User::create($data);
        $user->assignRole('customer');

        return $user;
    }

    /**
     * Update an existing customer.
     */
    public function update(string $id, array $data)
    {
        $user = $this->findById($id);
        
        // Remove password from update if it is empty
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * Delete a customer.
     */
    public function delete(string $id)
    {
        $user = $this->findById($id);
        return $user->delete();
    }

    /**
     * Get summary statistics of customers.
     */
    public function getSummaryStats()
    {
        $customers = User::role('customer')->get();
        $totalCustomers = $customers->count();
        $activeCustomers = $customers->where('is_active', '1')->count();
        $vipCustomers = $customers->where('tier', 'VIP')->count();
        $newCustomers = $customers->where('tier', 'Baru')->count();

        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'vip_customers' => $vipCustomers,
            'new_customers' => $newCustomers,
        ];
    }
}
