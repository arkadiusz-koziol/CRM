import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { TasksScreen } from '@/features/tasks/screens/TasksScreen';
import { TaskDetailScreen } from '@/features/tasks/screens/TaskDetailScreen';
import { UsersScreen } from '@/features/users/screens/UsersScreen';
import { UserDetailScreen } from '@/features/users/screens/UserDetailScreen';
import { ProfileScreen } from '@/features/profile/screens/ProfileScreen';
import { SettingsScreen } from '@/features/settings/screens/SettingsScreen';

export type MainTabParamList = {
  Tasks: undefined;
  Users: undefined;
  Profile: undefined;
  Settings: undefined;
};

export type TasksStackParamList = {
  TasksList: undefined;
  TaskDetail: { taskId: string };
};

export type UsersStackParamList = {
  UsersList: undefined;
  UserDetail: { userId: string };
};

const Tab = createBottomTabNavigator<MainTabParamList>();
const TasksStack = createNativeStackNavigator<TasksStackParamList>();
const UsersStack = createNativeStackNavigator<UsersStackParamList>();

function TasksStackNavigator(): React.JSX.Element {
  return (
    <TasksStack.Navigator>
      <TasksStack.Screen
        name="TasksList"
        component={TasksScreen}
        options={{ title: 'Tasks' }}
      />
      <TasksStack.Screen
        name="TaskDetail"
        component={TaskDetailScreen}
        options={{ title: 'Task Details' }}
      />
    </TasksStack.Navigator>
  );
}

function UsersStackNavigator(): React.JSX.Element {
  return (
    <UsersStack.Navigator>
      <UsersStack.Screen
        name="UsersList"
        component={UsersScreen}
        options={{ title: 'Users' }}
      />
      <UsersStack.Screen
        name="UserDetail"
        component={UserDetailScreen}
        options={{ title: 'User Details' }}
      />
    </UsersStack.Navigator>
  );
}

export function MainNavigator(): React.JSX.Element {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarStyle: {
          paddingBottom: 5,
          paddingTop: 5,
          height: 60,
        },
      }}
    >
      <Tab.Screen
        name="Tasks"
        component={TasksStackNavigator}
        options={{
          tabBarLabel: 'Tasks',
        }}
      />
      <Tab.Screen
        name="Users"
        component={UsersStackNavigator}
        options={{
          tabBarLabel: 'Users',
        }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{
          tabBarLabel: 'Profile',
        }}
      />
      <Tab.Screen
        name="Settings"
        component={SettingsScreen}
        options={{
          tabBarLabel: 'Settings',
        }}
      />
    </Tab.Navigator>
  );
}
