<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    order: Object,
    statuses: Array
});

const form = useForm({
    status: props.order.status,
    notes: props.order.notes || '',
});

const submit = () => {
    form.put(route('admin.orders.update', props.order.id), {
        preserveScroll: true
    });
};

// Function to determine status badge color
const getStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        case 'shipped':
            return 'bg-indigo-100 text-indigo-800';
        case 'delivered':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head :title="`Edit Order #${order.order_number}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-coffee-800 leading-tight">
                    Edit Order #{{ order.order_number }}
                </h2>
                <div class="flex space-x-4">
                    <Link :href="route('admin.orders.index')" class="px-4 py-2 bg-coffee-200 text-coffee-800 rounded hover:bg-coffee-300 transition">
                        Back to Orders
                    </Link>
                    <Link :href="route('admin.orders.show', order.id)" class="px-4 py-2 bg-coffee-200 text-coffee-800 rounded hover:bg-coffee-300 transition">
                        View Order
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12 bg-cream-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Update Form -->
                    <div class="md:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border border-coffee-200">
                                <h3 class="text-lg font-semibold text-coffee-800 mb-6">Update Order Status</h3>
                                
                                <form @submit.prevent="submit" class="space-y-6">
                                    <div>
                                        <InputLabel for="status" value="Status" />
                                        <select 
                                            id="status" 
                                            v-model="form.status" 
                                            class="mt-1 block w-full border-gray-300 focus:border-coffee-500 focus:ring-coffee-500 rounded-md shadow-sm"
                                        >
                                            <option v-for="status in statuses" :key="status" :value="status">
                                                {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.status" />
                                    </div>
                                    
                                    <div>
                                        <InputLabel for="notes" value="Order Notes" />
                                        <textarea 
                                            id="notes" 
                                            v-model="form.notes" 
                                            class="mt-1 block w-full border-gray-300 focus:border-coffee-500 focus:ring-coffee-500 rounded-md shadow-sm"
                                            rows="4"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.notes" />
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <PrimaryButton :disabled="form.processing">
                                            Update Order
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border border-coffee-200">
                                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Order Summary</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Order Number:</span>
                                        <span class="text-coffee-800">{{ order.order_number }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Date:</span>
                                        <span class="text-coffee-800">{{ new Date(order.created_at).toLocaleDateString() }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Customer:</span>
                                        <span class="text-coffee-800">{{ order.user.name }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Email:</span>
                                        <span class="text-coffee-800">{{ order.user.email }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Payment Method:</span>
                                        <span class="text-coffee-800">{{ order.payment_method }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Payment Status:</span>
                                        <span class="px-2 py-1 rounded text-xs" :class="{
                                            'bg-green-100 text-green-800': order.payment_status === 'paid',
                                            'bg-yellow-100 text-yellow-800': order.payment_status === 'pending',
                                            'bg-red-100 text-red-800': order.payment_status === 'failed'
                                        }">
                                            {{ order.payment_status }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between border-b border-coffee-100 pb-2">
                                        <span class="text-coffee-600">Current Status:</span>
                                        <span class="px-2 py-1 rounded text-xs" :class="getStatusClass(order.status)">
                                            {{ order.status }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between pt-2">
                                        <span class="text-coffee-600 font-medium">Total Amount:</span>
                                        <span class="text-coffee-800 font-bold">€{{ order.total_amount }}</span>
                                    </div>
                                </div>
                                
                                <h4 class="text-md font-medium text-coffee-700 mt-6 mb-2">Items Summary:</h4>
                                <ul class="space-y-2">
                                    <li v-for="item in order.items" :key="item.id" class="flex justify-between text-sm">
                                        <span class="text-coffee-600">{{ item.quantity }}x {{ item.product ? item.product.name : 'Product unavailable' }}</span>
                                        <span class="text-coffee-800">€{{ item.subtotal }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>