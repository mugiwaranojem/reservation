import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import ReservationsDashboard from './components/ReservationsDashboard';

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<ReservationsDashboard />} />
        {/* Add more routes here */}
      </Routes>
    </Router>
  );
}

export default App;