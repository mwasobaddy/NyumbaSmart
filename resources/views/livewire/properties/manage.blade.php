<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Manage Properties</h1>

    <form wire:submit.prevent="{{ $property_id ? 'update' : 'create' }}" class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" wire:model="name" placeholder="Property Name" class="border p-2">
            <input type="text" wire:model="address" placeholder="Address" class="border p-2">
            <textarea wire:model="description" placeholder="Description" class="border p-2"></textarea>
            <input type="text" wire:model="logo_url" placeholder="Logo URL" class="border p-2">
            <input type="text" wire:model="theme_color" placeholder="Theme Color" class="border p-2">
            <input type="text" wire:model="app_name" placeholder="App Name" class="border p-2">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-4">
            {{ $property_id ? 'Update Property' : 'Add Property' }}
        </button>
    </form>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Name</th>
                <th class="border border-gray-300 px-4 py-2">Address</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($properties as $property)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $property->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $property->address }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button wire:click="edit({{ $property->id }})" class="bg-yellow-500 text-white px-2 py-1">Edit</button>
                        <button wire:click="delete({{ $property->id }})" class="bg-red-500 text-white px-2 py-1">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
