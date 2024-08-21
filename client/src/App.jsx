import { useState } from 'react';
import { toast, ToastContainer } from 'react-toastify';
import Login from './components/Login';
import TaskList from './components/TaskList';
import 'react-toastify/dist/ReactToastify.css';

function App() {
  const [user, setUser] = useState(null);

  const handleLogin = (user) => {
    setUser(user);
  };

  const handleLogout = () => {
    setUser(null);
    toast.info('Logged out successfully');
  };

  return (
    <div className="App min-h-screen flex flex-col items-center justify-center w-screen bg-gray-100">
      {user ? (
        <>
          <div className="mb-4 text-lg">
            Welcome, {user}!
            <button
              onClick={handleLogout}
              className="ml-4 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
            >
              Logout
            </button>
          </div>
          <TaskList />
        </>
      ) : (
        <Login onLoginSuccess={handleLogin} />
      )}
      <ToastContainer position="top-right" />
    </div>
  );
}

export default App;

//   return (
//     <div className="App min-h-screen flex items-center justify-center w-screen bg-gray-100">
//       <TaskList />
//       <ToastContainer position="top-right" />
//     </div>
//   );
// }

// export default App;
