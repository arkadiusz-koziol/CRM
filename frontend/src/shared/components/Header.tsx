import React from 'react'
import { Link } from 'react-router-dom'
import { Button } from '@/shared/ui/Button'
import { useAuth } from '@/hooks/useAuth'
import { 
  Home, 
  Users, 
  ClipboardList, 
  Package, 
  Wrench, 
  Building2, 
  Car,
  MapPin,
  LogOut
} from 'lucide-react'

export const Header: React.FC = () => {
  const { user, logout } = useAuth()
  
  const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: Home },
    { name: 'Tasks', href: '/tasks', icon: ClipboardList },
    { name: 'Users', href: '/users', icon: Users },
    { name: 'Materials', href: '/materials', icon: Package },
    { name: 'Tools', href: '/tools', icon: Wrench },
    { name: 'Estates', href: '/estates', icon: Building2 },
    { name: 'Cars', href: '/cars', icon: Car },
    { name: 'Cities', href: '/cities', icon: MapPin },
  ]

  return (
    <header className="bg-white shadow-sm border-b border-gray-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <div className="flex items-center">
            <Link to="/dashboard" className="flex items-center space-x-2">
              <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-sm">S</span>
              </div>
              <span className="text-xl font-bold text-gray-900">SkyTech</span>
            </Link>
          </div>

          {/* Navigation */}
          <nav className="hidden md:flex space-x-8">
            {navigation.map((item) => {
              const Icon = item.icon
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className="flex items-center space-x-1 text-gray-600 hover:text-blue-600 transition-colors"
                >
                  <Icon className="w-4 h-4" />
                  <span>{item.name}</span>
                </Link>
              )
            })}
          </nav>

          {/* User actions */}
          <div className="flex items-center space-x-4">
            <div className="text-sm text-gray-600">
              Welcome, {user?.name} {user?.surname}
            </div>
            <Button variant="outline" size="sm" onClick={logout}>
              <LogOut className="w-4 h-4 mr-2" />
              Logout
            </Button>
          </div>
        </div>
      </div>
    </header>
  )
}
