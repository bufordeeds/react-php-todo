import { useState } from 'react';

const TaskForm = ({ onAddTask }) => {
	const [title, setTitle] = useState('');

	const handleSubmit = (e) => {
		e.preventDefault();
		if (title.trim()) {
			onAddTask(title);
			setTitle('');
		}
	};

	return (
		<form onSubmit={handleSubmit} className='mb-4'>
			<input
				type='text'
				value={title}
				onChange={(e) => setTitle(e.target.value)}
				placeholder='Enter a new task'
				className='w-full p-2 border border-gray-300 rounded'
			/>
			<button
				type='submit'
				className='mt-2 w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600'
			>
				Add Task
			</button>
		</form>
	);
};

export default TaskForm;
