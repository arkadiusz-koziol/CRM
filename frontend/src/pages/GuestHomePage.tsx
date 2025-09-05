import React from 'react'
import { Link } from 'react-router-dom'
import { Button } from '@/shared/ui/Button'
import { Card, CardContent } from '@/shared/ui/Card'
import { 
  ClipboardList, 
  Users, 
  Package, 
  Building2, 
  Car,
  MapPin,
  ArrowRight,
  Shield,
  Clock,
  CheckCircle
} from 'lucide-react'

export const GuestHomePage: React.FC = () => {
  const features = [
    {
      icon: ClipboardList,
      title: 'Task Management',
      description: 'Efficiently manage and track field service tasks with real-time updates and status tracking.'
    },
    {
      icon: Users,
      title: 'Team Coordination',
      description: 'Coordinate your team members with role-based access and assignment capabilities.'
    },
    {
      icon: Package,
      title: 'Inventory Management',
      description: 'Keep track of materials and tools with comprehensive inventory management system.'
    },
    {
      icon: Building2,
      title: 'Estate Management',
      description: 'Manage multiple estates and properties with detailed location and contact information.'
    },
    {
      icon: Car,
      title: 'Fleet Management',
      description: 'Track and manage your vehicle fleet with maintenance schedules and usage monitoring.'
    },
    {
      icon: MapPin,
      title: 'Location Services',
      description: 'Geographic organization with city-based management and location tracking.'
    }
  ]

  const benefits = [
    {
      icon: Shield,
      title: 'Secure & Reliable',
      description: 'Enterprise-grade security with reliable data protection and backup systems.'
    },
    {
      icon: Clock,
      title: 'Real-time Updates',
      description: 'Stay informed with real-time notifications and status updates across all operations.'
    },
    {
      icon: CheckCircle,
      title: 'Streamlined Workflow',
      description: 'Optimize your field service operations with automated workflows and processes.'
    }
  ]

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-sm">S</span>
              </div>
              <span className="text-xl font-bold text-gray-900">SkyTech</span>
            </div>
            <div className="flex items-center space-x-4">
              <Link to="/login">
                <Button variant="outline" size="sm">
                  Sign In
                </Button>
              </Link>
              <Link to="/login">
                <Button size="sm">
                  Get Started
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div className="text-center">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              Field Service Management
              <span className="block text-blue-200">Made Simple</span>
            </h1>
            <p className="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
              Streamline your field operations with our comprehensive CRM platform. 
              Manage tasks, teams, inventory, and more from one centralized system.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link to="/login">
                <Button size="lg" className="bg-white text-blue-600 hover:bg-gray-100">
                  Start Free Trial
                  <ArrowRight className="w-5 h-5 ml-2" />
                </Button>
              </Link>
              <Button variant="outline" size="lg" className="border-white text-white hover:bg-white hover:text-blue-600">
                Watch Demo
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-24 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Everything You Need to Manage Field Services
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Our platform provides all the tools you need to efficiently manage your field service operations.
            </p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((feature, index) => {
              const Icon = feature.icon
              return (
                <Card key={index} className="text-center hover:shadow-lg transition-shadow">
                  <CardContent>
                    <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                      <Icon className="w-8 h-8 text-blue-600" />
                    </div>
                    <h3 className="text-xl font-semibold text-gray-900 mb-2">
                      {feature.title}
                    </h3>
                    <p className="text-gray-600">
                      {feature.description}
                    </p>
                  </CardContent>
                </Card>
              )
            })}
          </div>
        </div>
      </section>

      {/* Benefits Section */}
      <section className="py-24 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Why Choose SkyTech?
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Built for field service teams who need reliability, efficiency, and scalability.
            </p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {benefits.map((benefit, index) => {
              const Icon = benefit.icon
              return (
                <div key={index} className="text-center">
                  <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Icon className="w-8 h-8 text-green-600" />
                  </div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-2">
                    {benefit.title}
                  </h3>
                  <p className="text-gray-600">
                    {benefit.description}
                  </p>
                </div>
              )
            })}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-24 bg-blue-600 text-white">
        <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl md:text-4xl font-bold mb-4">
            Ready to Transform Your Field Operations?
          </h2>
          <p className="text-xl text-blue-100 mb-8">
            Join thousands of teams already using SkyTech to streamline their field service management.
          </p>
          <Link to="/login">
            <Button size="lg" className="bg-white text-blue-600 hover:bg-gray-100">
              Get Started Today
              <ArrowRight className="w-5 h-5 ml-2" />
            </Button>
          </Link>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-900 text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <div className="flex items-center space-x-2 mb-4 md:mb-0">
              <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-sm">S</span>
              </div>
              <span className="text-xl font-bold">SkyTech</span>
            </div>
            <div className="text-gray-400 text-sm">
              © 2024 SkyTech. All rights reserved.
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}
