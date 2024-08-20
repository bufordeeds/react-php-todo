const Task = ({ task, onToggleComplete, onDelete }) => {
	return (
		<div className='flex items-center justify-between p-4 bg-white shadow-md rounded-lg mb-2'>
			<div className='flex items-center'>
				<input
					type='checkbox'
					checked={task.status}
					onChange={() => onToggleComplete(task.id)}
					className='mr-2'
				/>
				<span
					className={task.status ? 'line-through text-gray-500' : ''}
				>
					{task.title}
				</span>
			</div>
			<button
				onClick={() => onDelete(task.id)}
				className='bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600'
			>
				Delete
			</button>
		</div>
	);
};

export default Task;
