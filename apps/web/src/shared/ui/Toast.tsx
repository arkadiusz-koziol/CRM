    'use client';

import { useEffect, useState } from 'react';

interface ToastProps {
  message: string;
  type: 'success' | 'error' | 'info';
  duration?: number;
  onClose: () => void;
}

export function Toast({ message, type, duration = 2000, onClose }: ToastProps) {
  const [isVisible, setIsVisible] = useState(true);

  useEffect(() => {
    const timer = setTimeout(() => {
      setIsVisible(false);
      setTimeout(onClose, 300);
    }, duration);

    return () => clearTimeout(timer);
  }, [duration, onClose]);

  const getToastStyles = () => {
    switch (type) {
      case 'success':
        return '!bg-gradient-to-r !from-green-500 !to-green-400 !border-green-600 !text-white !shadow-2xl';
      case 'error':
        return '!bg-gradient-to-r !from-red-500 !to-red-400 !border-red-600 !text-white !shadow-2xl';
      case 'info':
        return '!bg-gradient-to-r !from-blue-500 !to-blue-400 !border-blue-600 !text-white !shadow-2xl';
      default:
        return '!bg-gradient-to-r !from-gray-500 !to-gray-400 !border-gray-600 !text-white !shadow-2xl';
    }
  };

  const getIcon = () => {
    switch (type) {
      case 'success':
        return (
          <div className="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
            <svg className="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
            </svg>
          </div>
        );
      case 'error':
        return (
          <div className="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
            <svg className="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
            </svg>
          </div>
        );
      case 'info':
        return (
          <div className="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
            <svg className="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
            </svg>
          </div>
        );
      default:
        return null;
    }
  };

  if (!isVisible) return null;

  const getInlineStyles = () => {
    switch (type) {
      case 'success':
        return { background: 'linear-gradient(to right, #10b981, #34d399)', color: 'white' };
      case 'error':
        return { background: 'linear-gradient(to right, #ef4444, #f87171)', color: 'white' };
      case 'info':
        return { background: 'linear-gradient(to right, #3b82f6, #60a5fa)', color: 'white' };
      default:
        return { background: 'linear-gradient(to right, #6b7280, #9ca3af)', color: 'white' };
    }
  };

  return (
    <div 
      className={`fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-xs w-full ${getToastStyles()} border-2 rounded-lg p-4 shadow-lg transition-all duration-300 ${
        isVisible ? 'translate-y-0 opacity-100 scale-100' : '-translate-y-8 opacity-0 scale-95'
      }`}
      style={getInlineStyles()}
    >
      <div className="flex items-center">
        <div className="flex-shrink-0">
          {getIcon()}
        </div>
        <div className="ml-3 flex-1">
          <p className="!font-semibold !text-sm !text-white" style={{ color: 'white', fontWeight: '600', fontSize: '0.875rem' }}>{message}</p>
        </div>
        <div className="ml-4 flex-shrink-0">
          <button
            onClick={() => {
              setIsVisible(false);
              setTimeout(onClose, 300);
            }}
            className="inline-flex !text-white hover:bg-white/20 rounded-full p-0.5 focus:outline-none focus:ring-2 focus:ring-white/50"
            style={{ color: 'white' }}
          >
            <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  );
}
