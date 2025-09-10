'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { useCreateEstate } from '../api/useCreateEstate';

export default function CreateEstateForm() {
  const [formData, setFormData] = useState({
    name: '',
    custom_id: '',
    street: '',
    postal_code: '',
    house_number: '',
    city: 1, // Default to first city
  });

  const router = useRouter();
  const createMutation = useCreateEstate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    try {
      await createMutation.mutateAsync(formData);
      router.push('/estates');
    } catch (error) {
      console.error('Failed to create estate:', error);
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: name === 'city' ? Number(value) : value,
    }));
  };

  return (
    <div className="max-w-md mx-auto mt-8">
      <div className="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 className="text-2xl font-bold mb-6 text-gray-800">Dodaj nową nieruchomość</h2>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
              Nazwa
            </label>
            <input
              type="text"
              id="name"
              name="name"
              value={formData.name}
              onChange={handleChange}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Wprowadź nazwę nieruchomości"
            />
          </div>

          <div>
            <label htmlFor="custom_id" className="block text-sm font-medium text-gray-700 mb-1">
              ID niestandardowe
            </label>
            <input
              type="text"
              id="custom_id"
              name="custom_id"
              value={formData.custom_id}
              onChange={handleChange}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Wprowadź ID niestandardowe"
            />
          </div>

          <div>
            <label htmlFor="street" className="block text-sm font-medium text-gray-700 mb-1">
              Ulica
            </label>
            <input
              type="text"
              id="street"
              name="street"
              value={formData.street}
              onChange={handleChange}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Wprowadź nazwę ulicy"
            />
          </div>

          <div>
            <label htmlFor="postal_code" className="block text-sm font-medium text-gray-700 mb-1">
              Kod pocztowy
            </label>
            <input
              type="text"
              id="postal_code"
              name="postal_code"
              value={formData.postal_code}
              onChange={handleChange}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="00-000"
            />
          </div>

          <div>
            <label htmlFor="house_number" className="block text-sm font-medium text-gray-700 mb-1">
              Numer domu
            </label>
            <input
              type="text"
              id="house_number"
              name="house_number"
              value={formData.house_number}
              onChange={handleChange}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="1A/2"
            />
          </div>

          <div>
            <label htmlFor="city" className="block text-sm font-medium text-gray-700 mb-1">
              Miasto
            </label>
            <select
              id="city"
              name="city"
              value={formData.city}
              onChange={handleChange}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
              <option value={1}>Warszawa</option>
              <option value={2}>Kraków</option>
              <option value={4}>Wrocław</option>
              <option value={5}>Poznań</option>
              <option value={6}>Gdańsk</option>
            </select>
          </div>

          <div className="flex items-center justify-between pt-4">
            <button
              type="button"
              onClick={() => router.push('/estates')}
              className="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            >
              Anuluj
            </button>
            <button
              type="submit"
              disabled={createMutation.isPending}
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50"
            >
              {createMutation.isPending ? 'Dodawanie...' : 'Dodaj nieruchomość'}
            </button>
          </div>
        </form>

        {createMutation.isError && (
          <div className="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            Wystąpił błąd podczas dodawania nieruchomości. Spróbuj ponownie.
          </div>
        )}
      </div>
    </div>
  );
}
