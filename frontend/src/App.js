import { useEffect, useState } from 'react';
import axios from 'axios';

function App() {
  const [message, setMessage] = useState('');

  useEffect(() => {
    axios.get('http://localhost:8000/api/hello')
      .then(response => setMessage(response.data.message))
      .catch(error => {
        console.error('API call failed:', error);
        setMessage('API call failed');
      });
  }, []);

  return (
    <div className="App">
      <h1>Hello, World!</h1>
    </div>
  );
}

export default App;
