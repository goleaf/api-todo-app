# Real-Time Task Notifications

The Todo application includes a real-time notification system that alerts users when tasks are created, updated, or completed. This feature leverages Laravel's event broadcasting system, Livewire, and Pusher for seamless real-time updates.

## How It Works

1. **Event Broadcasting**: When a task is created, updated, or completed, the application broadcasts an event to a private channel specific to the user.

2. **Livewire Component**: The `TaskNotifications` Livewire component listens for these events and updates the UI in real-time.

3. **Private Channels**: Each user receives notifications only for their own tasks, ensuring data privacy and security.

## Events

The application uses the following events:

- `TaskCreated`: Dispatched when a new task is created
- `TaskUpdated`: Dispatched when a task is updated
- `TaskCompleted`: Dispatched when a task is marked as completed

## Implementation Details

### Frontend Component

The `TaskNotifications` Livewire component handles:

- Displaying notifications in a dropdown panel
- Managing notification count
- Allowing users to dismiss notifications
- Limiting the maximum number of stored notifications
- Showing task details directly in the notification

### Broadcasting Configuration

For development, we use a simple log driver to see events in the application logs:

```env
BROADCAST_DRIVER=log
```

For production, Pusher is recommended for reliable real-time communication:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1
```

## Setting Up for Production

1. **Sign up for Pusher**:
   - Create an account at [https://pusher.com/](https://pusher.com/)
   - Create a new Channels app
   - Copy your app credentials (App ID, Key, Secret, Cluster)

2. **Update Environment Variables**:
   - Set `BROADCAST_DRIVER=pusher`
   - Add your Pusher credentials to the `.env` file

   ```
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your-app-id
   PUSHER_APP_KEY=your-app-key
   PUSHER_APP_SECRET=your-app-secret
   PUSHER_APP_CLUSTER=mt1
   
   VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
   VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
   VITE_BROADCAST_DRIVER=pusher
   ```

3. **Rebuild Assets**:
   ```bash
   npm run build
   ```

4. **Clear Cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Testing Real-Time Notifications

1. **Development Testing**:
   - With the log driver, events are recorded in your Laravel logs
   - Check `storage/logs/laravel.log` to see event data

2. **Production Testing**:
   - With Pusher configured, open the application in two different browsers
   - Log in with the same user in both browsers
   - Create or update a task in one browser
   - Observe the notification appearing in the other browser

## Troubleshooting

- **Missing Notifications**: Ensure your `.env` file has the correct Pusher credentials
- **CORS Issues**: Make sure your Pusher settings allow your application domain
- **Console Errors**: Check the browser console for WebSocket connection errors

## Security Considerations

- Events are broadcast to private channels (`user.{id}`)
- Channel authorization ensures users only receive their own notifications
- Sensitive task data is filtered before broadcasting

## Extending the System

To add new notification types:

1. Create a new event class that implements `ShouldBroadcast`
2. Add a new event handler method in the `TaskNotifications` component
3. Update the notification rendering in the blade template 