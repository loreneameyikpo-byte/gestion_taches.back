export interface User {
  id: number
  name: string
  email: string
  created_at: string
}

export interface Project {
  id: number
  name: string
  description: string | null
  start_date: string | null
  due_date: string | null
  tasks_count?: number
  created_at: string
  updated_at: string
}

export type TaskPriority = 'low' | 'medium' | 'high'
export type TaskStatus = 'todo' | 'in_progress' | 'done'

export interface Task {
  id: number
  project_id: number
  project_name?: string
  title: string
  description: string | null
  priority: TaskPriority
  due_date: string | null
  status: TaskStatus
  position: number
  created_at: string
  updated_at: string
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    total: number
  }
}

export interface DashboardStats {
  total_projects: number
  total_tasks: number
  completed_tasks: number
  in_progress_tasks: number
  todo_tasks: number
  tasks_by_priority: {
    low: number
    medium: number
    high: number
  }
}

export interface ApiValidationError {
  message: string
  errors: Record<string, string[]>
}