'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { getUser, signOut } from '@/shared/lib/auth';
import { useActivitiesQuery } from '@/features/activities/api/useActivitiesQuery';
import Link from 'next/link';

// SVG Icons
const ToolsIcon = () => (
  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
  </svg>
);

const MaterialsIcon = () => (
  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
  </svg>
);

const CarsIcon = () => (
  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
  </svg>
);

const EstatesIcon = () => (
  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
  </svg>
);

const AnalyticsIcon = () => (
  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
  </svg>
);

const ProfileIcon = () => (
  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
  </svg>
);

const LogoutIcon = () => (
  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
  </svg>
);

const NotificationIcon = () => (
  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L16 7l-6 6-6-6z" />
  </svg>
);

export default function DashboardPage() {
  const [user, setUser] = useState<any>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [currentTime, setCurrentTime] = useState(new Date());
  const router = useRouter();
  const { data: activitiesData, isLoading: activitiesLoading, error: activitiesError } = useActivitiesQuery(5);

  useEffect(() => {
    loadUser();
    
    // Update time every minute
    const timer = setInterval(() => {
      setCurrentTime(new Date());
    }, 60000);

    return () => clearInterval(timer);
  }, []);

  const loadUser = async () => {
    try {
      const userData = await getUser();
      if (!userData) {
        router.push('/login');
        return;
      }
      setUser(userData);
    } catch (error) {
      console.error('Failed to load user:', error);
      router.push('/login');
    }
  };

  const handleSignOut = async () => {
    try {
      await signOut();
      router.push('/login');
    } catch (error) {
      console.error('Sign out failed:', error);
    }
  };

  const formatTime = (date: Date) => {
    return date.toLocaleTimeString('pl-PL', { 
      hour: '2-digit', 
      minute: '2-digit' 
    });
  };

  const formatDate = (date: Date) => {
    return date.toLocaleDateString('pl-PL', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  };

  const dashboardCards = [
    {
      title: 'Narzędzia',
      description: 'Zarządzaj narzędziami',
      href: '/tools',
      icon: ToolsIcon,
      gradient: 'from-blue-500 to-blue-600',
      hoverGradient: 'from-blue-600 to-blue-700',
      stats: '24 aktywne',
      color: 'blue'
    },
    {
      title: 'Materiały',
      description: 'Zarządzaj materiałami',
      href: '/materials',
      icon: MaterialsIcon,
      gradient: 'from-emerald-500 to-emerald-600',
      hoverGradient: 'from-emerald-600 to-emerald-700',
      stats: '156 pozycji',
      color: 'emerald'
    },
    {
      title: 'Pojazdy',
      description: 'Zarządzaj pojazdami',
      href: '/cars',
      icon: CarsIcon,
      gradient: 'from-amber-500 to-amber-600',
      hoverGradient: 'from-amber-600 to-amber-700',
      stats: '8 pojazdów',
      color: 'amber'
    },
    {
      title: 'Nieruchomości',
      description: 'Zarządzaj nieruchomościami',
      href: '/estates',
      icon: EstatesIcon,
      gradient: 'from-purple-500 to-purple-600',
      hoverGradient: 'from-purple-600 to-purple-700',
      stats: '12 obiektów',
      color: 'purple'
    },
    {
      title: 'Analytics',
      description: 'Raporty i analizy',
      href: '/analytics',
      icon: AnalyticsIcon,
      gradient: 'from-indigo-500 to-indigo-600',
      hoverGradient: 'from-indigo-600 to-indigo-700',
      stats: 'Wzrost 15%',
      color: 'indigo'
    },
    {
      title: 'Profil',
      description: 'Ustawienia konta',
      href: '/profile',
      icon: ProfileIcon,
      gradient: 'from-pink-500 to-pink-600',
      hoverGradient: 'from-pink-600 to-pink-700',
      stats: 'Aktywny',
      color: 'pink'
    }
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
      {/* Header */}
      <header className="bg-white/80 backdrop-blur-md shadow-lg border-b border-white/20 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-4">
            <div className="flex items-center space-x-4">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                  <span className="text-white font-bold text-lg">S</span>
                </div>
                <div>
                  <h1 className="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                    Skytech CRM
                  </h1>
                  <p className="text-sm text-gray-500">System zarządzania</p>
                </div>
              </div>
            </div>
            
            <div className="flex items-center space-x-6">
              {/* Time Display */}
              <div className="text-right">
                <div className="text-2xl font-bold text-gray-900">
                  {formatTime(currentTime)}
                </div>
                <div className="text-sm text-gray-500">
                  {formatDate(currentTime)}
                </div>
              </div>

              {/* Notifications */}
              <button className="relative p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <NotificationIcon />
                <span className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
              </button>

              {/* User Menu */}
              <div className="flex items-center space-x-3">
                <div className="text-right">
                  <div className="text-sm font-medium text-gray-900">
                    {user?.name || 'Użytkownik'}
                  </div>
                  <div className="text-xs text-gray-500">
                    {user?.email || 'admin@example.com'}
                  </div>
                </div>
                <button
                  onClick={handleSignOut}
                  className="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg text-sm font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                  <LogoutIcon />
                  <span>Wyloguj</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {/* Welcome Section */}
        <div className="mb-8">
          <div className="bg-white/60 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/20">
            <div className="flex items-center justify-between">
              <div>
                <h2 className="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent mb-2">
                  Witaj w Skytech CRM!
                </h2>
                <p className="text-lg text-gray-600 mb-4">
                  Zarządzaj swoimi projektami, narzędziami i zespołem w jednym miejscu
                </p>
                <div className="flex items-center space-x-6 text-sm text-gray-500">
                  <div className="flex items-center space-x-2">
                    <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>System online</span>
                  </div>
                  <div className="flex items-center space-x-2">
                    <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <span>Ostatnia aktualizacja: {formatTime(currentTime)}</span>
                  </div>
                </div>
              </div>
              <div className="hidden lg:block">
                <div className="w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-full flex items-center justify-center">
                  <div className="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                    <span className="text-white text-3xl font-bold">CRM</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Dashboard Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          {dashboardCards.map((card, index) => {
            const IconComponent = card.icon;
            return (
              <a
                key={card.title}
                href={card.href}
                className="group relative overflow-hidden bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-white/20 block"
                style={{
                  animationDelay: `${index * 100}ms`
                }}
              >
                <div className="absolute inset-0 bg-gradient-to-br from-white/50 to-transparent"></div>
                <div className="relative p-6">
                  <div className="flex items-start justify-between mb-4">
                    <div className={`p-3 rounded-xl bg-gradient-to-r ${card.gradient} shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-110`}>
                      <IconComponent />
                    </div>
                    <div className="text-right">
                      <div className={`text-xs font-semibold px-2 py-1 rounded-full bg-${card.color}-100 text-${card.color}-800`}>
                        {card.stats}
                      </div>
                    </div>
                  </div>
                  
                  <h3 className="text-xl font-bold text-gray-900 mb-2 group-hover:text-gray-700 transition-colors">
                    {card.title}
                  </h3>
                  <p className="text-gray-600 mb-4 group-hover:text-gray-500 transition-colors">
                    {card.description}
                  </p>
                  
                  <div className="flex items-center text-sm font-medium text-gray-500 group-hover:text-gray-700 transition-colors">
                    <span>Przejdź do sekcji</span>
                    <svg className="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                    </svg>
                  </div>
                </div>
                
                {/* Hover Effect */}
                <div className={`absolute inset-0 bg-gradient-to-r ${card.hoverGradient} opacity-0 group-hover:opacity-5 transition-opacity duration-300`}></div>
              </a>
            );
          })}
        </div>

        {/* Quick Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="bg-white/70 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Aktywne projekty</p>
                <p className="text-3xl font-bold text-gray-900">12</p>
              </div>
              <div className="p-3 bg-green-100 rounded-full">
                <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div className="mt-4">
              <div className="flex items-center text-sm text-green-600">
                <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clipRule="evenodd" />
                </svg>
                <span>+2.5% z poprzedniego miesiąca</span>
              </div>
            </div>
          </div>

          <div className="bg-white/70 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Zespół</p>
                <p className="text-3xl font-bold text-gray-900">24</p>
              </div>
              <div className="p-3 bg-blue-100 rounded-full">
                <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
            <div className="mt-4">
              <div className="flex items-center text-sm text-blue-600">
                <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clipRule="evenodd" />
                </svg>
                <span>+3 nowych członków</span>
              </div>
            </div>
          </div>

          <div className="bg-white/70 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Wydajność</p>
                <p className="text-3xl font-bold text-gray-900">94%</p>
              </div>
              <div className="p-3 bg-purple-100 rounded-full">
                <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
            </div>
            <div className="mt-4">
              <div className="flex items-center text-sm text-purple-600">
                <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clipRule="evenodd" />
                </svg>
                <span>+5.2% w tym tygodniu</span>
              </div>
            </div>
          </div>
        </div>

        {/* Recent Activity */}
        <div className="bg-white/70 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-white/20">
          <h3 className="text-2xl font-bold text-gray-900 mb-6">Ostatnia aktywność</h3>
          <div className="space-y-4">
            {activitiesLoading ? (
              <div className="flex items-center justify-center py-8">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
              </div>
            ) : activitiesError ? (
              <div className="text-center py-4">
                <p className="text-red-500 text-sm">Błąd podczas ładowania aktywności</p>
              </div>
            ) : activitiesData?.data?.length ? (
              activitiesData.data.map((activity, index) => (
                <div key={activity.id} className="flex items-center space-x-4 p-4 rounded-xl hover:bg-white/50 transition-colors duration-200">
                  <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                    activity.entity_type === 'tools' ? 'bg-blue-100' :
                    activity.entity_type === 'materials' ? 'bg-emerald-100' :
                    activity.entity_type === 'cars' ? 'bg-amber-100' :
                    'bg-purple-100'
                  }`}>
                    {activity.entity_type === 'tools' && <ToolsIcon />}
                    {activity.entity_type === 'materials' && <MaterialsIcon />}
                    {activity.entity_type === 'cars' && <CarsIcon />}
                    {activity.entity_type === 'estates' && <EstatesIcon />}
                  </div>
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{activity.action}</p>
                    <p className="text-xs text-gray-500">{activity.user_name} • {activity.time_ago}</p>
                  </div>
                  <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                </div>
              ))
            ) : (
              <div className="text-center py-4">
                <p className="text-gray-500 text-sm">Brak aktywności</p>
              </div>
            )}
          </div>
        </div>
      </main>
    </div>
  );
}
