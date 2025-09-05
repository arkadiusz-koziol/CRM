import React from 'react'
import { Link } from 'react-router-dom'
import { Card, CardHeader, CardTitle, CardContent } from '@/shared/ui/Card'
import { Button } from '@/shared/ui/Button'
import { 
  ClipboardList, 
  Users, 
  Package, 
  Wrench, 
  Building2, 
  Car,
  MapPin,
  TrendingUp,
  Clock,
  CheckCircle,
  AlertCircle
} from 'lucide-react'

export const DashboardPage: React.FC = () => {
  const stats = [
    {
      name: 'Total Tasks',
      value: '24',
      change: '+4.75%',
      changeType: 'positive' as const,
      icon: ClipboardList,
    },
    {
      name: 'Active Users',
      value: '12',
      change: '+2.02%',
      changeType: 'positive' as const,
      icon: Users,
    },
    {
      name: 'Materials Stock',
      value: '1,234',
      change: '-0.1%',
      changeType: 'negative' as const,
      icon: Package,
    },
    {
      name: 'Available Tools',
      value: '89',
      change: '+1.25%',
      changeType: 'positive' as const,
      icon: Wrench,
    },
  ]

  const quickActions = [
    {
      name: 'Create Task',
      description: 'Assign a new task to a team member',
      href: '/tasks/create',
      icon: ClipboardList,
      color: 'bg-blue-500',
    },
    {
      name: 'Add Material',
      description: 'Add new material to inventory',
      href: '/materials/create',
      icon: Package,
      color: 'bg-green-500',
    },
    {
      name: 'Register Estate',
      description: 'Add a new estate to the system',
      href: '/estates/create',
      icon: Building2,
      color: 'bg-purple-500',
    },
    {
      name: 'Add Tool',
      description: 'Register a new tool',
      href: '/tools/create',
      icon: Wrench,
      color: 'bg-orange-500',
    },
  ]

  const recentTasks = [
    {
      id: 1,
      title: 'Fix network connectivity issue',
      priority: 'high',
      status: 'in_progress',
      assignedTo: 'John Doe',
      dueDate: '2024-01-15',
    },
    {
      id: 2,
      title: 'Install new equipment',
      priority: 'medium',
      status: 'pending',
      assignedTo: 'Jane Smith',
      dueDate: '2024-01-18',
    },
    {
      id: 3,
      title: 'Maintenance check',
      priority: 'low',
      status: 'completed',
      assignedTo: 'Mike Johnson',
      dueDate: '2024-01-12',
    },
  ]

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'high':
        return 'text-red-600 bg-red-50'
      case 'medium':
        return 'text-yellow-600 bg-yellow-50'
      case 'low':
        return 'text-green-600 bg-green-50'
      default:
        return 'text-gray-600 bg-gray-50'
    }
  }

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'completed':
        return <CheckCircle className="w-4 h-4 text-green-600" />
      case 'in_progress':
        return <Clock className="w-4 h-4 text-blue-600" />
      case 'pending':
        return <AlertCircle className="w-4 h-4 text-yellow-600" />
      default:
        return <AlertCircle className="w-4 h-4 text-gray-600" />
    }
  }

  return (
    <div className="space-y-8">
      {/* Welcome Section */}
      <div className="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-8 text-white">
        <h1 className="text-3xl font-bold mb-2">Welcome to SkyTech</h1>
        <p className="text-blue-100 text-lg">
          Your comprehensive field service management platform
        </p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => {
          const Icon = stat.icon
          return (
            <Card key={stat.name}>
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">{stat.name}</p>
                  <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                  <p className={`text-sm ${
                    stat.changeType === 'positive' ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {stat.change} from last month
                  </p>
                </div>
                <div className="p-3 bg-blue-50 rounded-lg">
                  <Icon className="w-6 h-6 text-blue-600" />
                </div>
              </div>
            </Card>
          )
        })}
      </div>

      {/* Quick Actions */}
      <div>
        <h2 className="text-2xl font-bold text-gray-900 mb-6">Quick Actions</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {quickActions.map((action) => {
            const Icon = action.icon
            return (
              <Card key={action.name} className="hover:shadow-md transition-shadow cursor-pointer">
                <Link to={action.href}>
                  <div className="flex items-center space-x-4">
                    <div className={`p-3 rounded-lg ${action.color}`}>
                      <Icon className="w-6 h-6 text-white" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{action.name}</h3>
                      <p className="text-sm text-gray-600">{action.description}</p>
                    </div>
                  </div>
                </Link>
              </Card>
            )
          })}
        </div>
      </div>

      {/* Recent Tasks */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <Card>
          <CardHeader>
            <CardTitle>Recent Tasks</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {recentTasks.map((task) => (
                <div key={task.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div className="flex items-center space-x-3">
                    {getStatusIcon(task.status)}
                    <div>
                      <p className="font-medium text-gray-900">{task.title}</p>
                      <p className="text-sm text-gray-600">Assigned to {task.assignedTo}</p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-2">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getPriorityColor(task.priority)}`}>
                      {task.priority}
                    </span>
                    <span className="text-sm text-gray-500">{task.dueDate}</span>
                  </div>
                </div>
              ))}
            </div>
            <div className="mt-4">
              <Button variant="outline" size="sm" className="w-full">
                <Link to="/tasks">View All Tasks</Link>
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* System Overview */}
        <Card>
          <CardHeader>
            <CardTitle>System Overview</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <Building2 className="w-5 h-5 text-blue-600" />
                  <span className="text-gray-700">Total Estates</span>
                </div>
                <span className="font-semibold">45</span>
              </div>
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <Car className="w-5 h-5 text-green-600" />
                  <span className="text-gray-700">Fleet Vehicles</span>
                </div>
                <span className="font-semibold">12</span>
              </div>
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <MapPin className="w-5 h-5 text-purple-600" />
                  <span className="text-gray-700">Cities Covered</span>
                </div>
                <span className="font-semibold">8</span>
              </div>
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <TrendingUp className="w-5 h-5 text-orange-600" />
                  <span className="text-gray-700">Tasks Completed</span>
                </div>
                <span className="font-semibold">156</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
