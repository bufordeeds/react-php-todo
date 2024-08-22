import { useEffect, useState } from 'react';
import Task from './Task';
import TaskForm from './TaskForm';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost/tasks.php';

const TaskList = () => {
  const [tasks, setTasks] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchTasks();
  }, []);

  const fetchTasks = async () => {
    setIsLoading(true);
    try {
      const response = await fetch(API_URL);
      if (!response.ok) {
        throw new Error('Failed to fetch tasks');
      }
      const data = await response.json();
      setTasks(data);
    } catch (err) {
      setError(err.message);
      toast.error('Failed to fetch tasks');
    } finally {
      setIsLoading(false);
    }
  };

  const addTask = async (title) => {
    try {
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title, completed: false }),
      });
      if (!response.ok) {
        throw new Error('Failed to add task');
      }
      const data = await response.json();
      setTasks((prevTasks) => [data.task, ...prevTasks]);
      toast.success(data.message);
    } catch (err) {
      toast.error(err.message);
    }
  };

  const toggleComplete = async (id) => {
    try {
      const task = tasks.find((t) => t.id === id);
      const response = await fetch(API_URL, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id, completed: !task.completed }),
      });
      if (!response.ok) {
        throw new Error('Failed to update task');
      }
      const data = await response.json();
      setTasks((prevTasks) => prevTasks.map((t) => (t.id === id ? data.task : t)));
      toast.success(data.message);
    } catch (err) {
      toast.error(err.message);
    }
  };

  const deleteTask = async (id) => {
    try {
      const response = await fetch(`${API_URL}?id=${id}`, {
        method: 'DELETE',
      });
      if (!response.ok) {
        throw new Error('Failed to delete task');
      }
      const data = await response.json();
      setTasks((prevTasks) => prevTasks.filter((task) => task.id !== id));
      toast.success(data.message);
    } catch (err) {
      toast.error(err.message);
    }
  };

  if (isLoading) return <div>Loading tasks...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div className="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
      <h1 className="text-2xl font-bold mb-4 text-center">Task Manager</h1>
      <TaskForm onAddTask={addTask} />
      {tasks.length === 0 ? (
        <p className="text-center text-gray-500 mt-4">No tasks yet. Add a task to get started!</p>
      ) : (
        tasks.map((task) => (
          <Task key={task.id} task={task} onToggleComplete={toggleComplete} onDelete={deleteTask} />
        ))
      )}
    </div>
  );
};

export default TaskList;
