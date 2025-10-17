'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { useUpdateTool } from '../api/useUpdateTool';
import { useToolQuery } from '../api/useToolQuery';
import { UpdateToolRequest } from '../api/useUpdateTool';
import { Toast } from '@/shared/ui/Toast';

interface EditToolFormProps {
  toolId: number;
}

export function EditToolForm({ toolId }: EditToolFormProps) {
  const router = useRouter();
  const updateTool = useUpdateTool();
  const { data: tool, isLoading, isError, error } = useToolQuery(toolId);
  const [formData, setFormData] = useState<UpdateToolRequest>({
    name: '',
    description: '',
    count: 0,
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [showToast, setShowToast] = useState(false);
  const [toastMessage, setToastMessage] = useState('');
  const [toastType, setToastType] = useState<'success' | 'error' | 'info'>('success');

  // Populate form when tool data is loaded
  useEffect(() => {
    if (tool) {
      setFormData({
        name: tool.name,
        description: tool.description,
        count: tool.count,
      });
    }
  }, [tool]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});

    try {
      await updateTool.mutateAsync({ toolId, data: formData });
      setToastMessage('🎉 Tool updated successfully!');
      setToastType('success');
      setShowToast(true);
      // Redirect after showing toast
      setTimeout(() => {
        router.push(`/tools/${toolId}`);
      }, 2000);
    } catch (error: any) {
      if (error.details?.errors) {
        setErrors(error.details.errors);
      } else {
        setErrors({ general: 'An error occurred while updating the tool.' });
        setToastMessage('❌ Failed to update tool. Please try again.');
        setToastType('error');
        setShowToast(true);
      }
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: name === 'count' ? parseInt(value) || 0 : value,
    }));
  };

  // Handle authentication error
  useEffect(() => {
    if (isError && error?.message === 'No access token available') {
      router.push('/login');
    }
  }, [isError, error, router]);

  if (isLoading) {
    return (
      <div className="flex justify-center items-center py-12">
        <div className="relative">
          <div className="w-12 h-12 border-4 border-purple-200 rounded-full animate-spin border-t-purple-600"></div>
          <div className="absolute top-0 left-0 w-12 h-12 border-4 border-transparent rounded-full animate-ping border-t-purple-400"></div>
        </div>
      </div>
    );
  }

  if (isError) {
    if (error?.message === 'No access token available') {
      return (
        <div className="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6 shadow-sm">
          <div className="flex items-center">
            <div className="flex-shrink-0">
              <div className="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                <svg className="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                </svg>
              </div>
            </div>
            <div className="ml-3">
              <p className="text-amber-800 font-medium">Redirecting to login...</p>
            </div>
          </div>
        </div>
      );
    }
    
    return (
      <div className="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-6 shadow-sm">
        <div className="flex items-center">
          <div className="flex-shrink-0">
            <div className="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
              <svg className="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
              </svg>
            </div>
          </div>
          <div className="ml-3">
            <p className="text-red-800 font-medium">Failed to load tool</p>
            <p className="text-sm text-red-600 mt-1">{error?.message}</p>
          </div>
        </div>
      </div>
    );
  }

  if (!tool) {
    return (
      <div className="text-center py-16">
        <div className="mx-auto w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mb-6">
          <svg className="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
          </svg>
        </div>
        <h3 className="text-xl font-semibold text-gray-900 mb-2">Tool not found</h3>
        <p className="text-gray-500 mb-8 max-w-sm mx-auto">The tool you're trying to edit doesn't exist or has been removed.</p>
        <button
          onClick={() => router.push('/tools')}
          className="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg"
        >
          <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Tools
        </button>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Toast Notification */}
      {showToast && (
        <Toast
          message={toastMessage}
          type={toastType}
          duration={3000}
          onClose={() => setShowToast(false)}
        />
      )}

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 className="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
            Edit Tool
          </h2>
          <p className="text-gray-600 mt-1">
            Update the tool information below
          </p>
        </div>
        <div className="flex gap-3">
          <button
            onClick={() => router.push(`/tools/${toolId}`)}
            className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200"
          >
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Cancel
          </button>
        </div>
      </div>

      {/* Form */}
      <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <form onSubmit={handleSubmit} className="p-8">

          <div className="space-y-6">
            {/* Tool Name */}
            <div>
              <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                Tool Name *
              </label>
              <input
                type="text"
                id="name"
                name="name"
                value={formData.name}
                onChange={handleChange}
                className={`w-full px-4 py-3 border rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 ${
                  errors.name ? 'border-red-300' : 'border-gray-300'
                }`}
                placeholder="Enter tool name"
                required
              />
              {errors.name && (
                <p className="mt-1 text-sm text-red-600">{errors.name}</p>
              )}
            </div>

            {/* Description */}
            <div>
              <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-2">
                Description *
              </label>
              <textarea
                id="description"
                name="description"
                value={formData.description}
                onChange={handleChange}
                rows={4}
                className={`w-full px-4 py-3 border rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 resize-none ${
                  errors.description ? 'border-red-300' : 'border-gray-300'
                }`}
                placeholder="Enter tool description"
                required
              />
              {errors.description && (
                <p className="mt-1 text-sm text-red-600">{errors.description}</p>
              )}
            </div>

            {/* Count */}
            <div>
              <label htmlFor="count" className="block text-sm font-medium text-gray-700 mb-2">
                Quantity *
              </label>
              <input
                type="number"
                id="count"
                name="count"
                value={formData.count}
                onChange={handleChange}
                min="0"
                className={`w-full px-4 py-3 border rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 ${
                  errors.count ? 'border-red-300' : 'border-gray-300'
                }`}
                placeholder="Enter quantity"
                required
              />
              {errors.count && (
                <p className="mt-1 text-sm text-red-600">{errors.count}</p>
              )}
            </div>
          </div>

          {/* Form Actions */}
          <div className="flex flex-col sm:flex-row gap-3 pt-8 border-t border-gray-100 mt-8">
            <button
              type="submit"
              disabled={updateTool.isPending}
              className="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              {updateTool.isPending ? (
                <>
                  <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Updating...
                </>
              ) : (
                <>
                  <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  Update Tool
                </>
              )}
            </button>
            <button
              type="button"
              onClick={() => router.push(`/tools/${toolId}`)}
              className="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200"
            >
              <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
