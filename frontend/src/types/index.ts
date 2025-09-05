// User types
export interface User {
  id: number
  name: string
  surname: string
  email: string
  phone: string
  created_at: string
  updated_at: string
}

// Task types
export enum TaskStatus {
  PENDING = 'pending',
  IN_PROGRESS = 'in_progress',
  COMPLETED = 'completed',
  CANCELLED = 'cancelled',
  ON_HOLD = 'on_hold'
}

export enum TaskPriority {
  LOW = 'low',
  MEDIUM = 'medium',
  HIGH = 'high',
  URGENT = 'urgent'
}

export interface Task {
  id: number
  title: string
  description: string
  status: TaskStatus
  priority: TaskPriority
  assigned_to: number
  created_by: number
  due_date?: string
  completed_at?: string
  estimated_hours?: number
  actual_hours?: number
  created_at: string
  updated_at: string
  assignedTo?: User
  createdBy?: User
}

// Material types
export interface Material {
  id: number
  name: string
  description: string
  count: number
  price: number
  created_at: string
  updated_at: string
}

// Tool types
export interface Tool {
  id: number
  name: string
  description: string
  count: number
  created_at: string
  updated_at: string
}

// Estate types
export interface Estate {
  id: number
  name: string
  custom_id: string
  street: string
  postal_code: string
  city_id: number
  house_number: string
  created_at: string
  updated_at: string
  city?: City
}

// City types
export interface City {
  id: number
  name: string
  created_at: string
  updated_at: string
}

// Car types
export interface Car {
  id: number
  name: string
  description: string
  registration_number: string
  technical_details: string
  created_at: string
  updated_at: string
}

// API Response types
export interface ApiResponse<T> {
  data: T
  message?: string
  status: number
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}
