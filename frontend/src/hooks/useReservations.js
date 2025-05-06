import { useState, useEffect } from 'react';
import axios from 'axios';

const API_BASE = 'http://localhost:8000/api';

export function useReservations() {
  const [reservations, setReservations] = useState([]);
  const [loading, setLoading] = useState(false);
  const [confirming, setConfirming] = useState(false);
  const [confirmMessage, setConfirmMessage] = useState('');

  const fetchReservations = async () => {
    setLoading(true);
    try {
      const response = await axios.get(`${API_BASE}/reservations`);
      setReservations(response.data.data);
    } catch (error) {
      console.error('Error fetching reservations', error);
    } finally {
      setLoading(false);
    }
  };

  const confirmReservation = async (pin) => {
    setConfirming(true);
    try {
      await axios.post(`${API_BASE}/reservations/${pin}/confirm`);
      setConfirmMessage('Reservation confirmed successfully.');
      await fetchReservations();
    } catch (error) {
      setConfirmMessage('Confirmation failed. Please try again later.');
    } finally {
      setConfirming(false);
    }
  };

  useEffect(() => {
    fetchReservations();
  }, []);

  return {
    reservations,
    loading,
    confirming,
    confirmMessage,
    confirmReservation,
    fetchReservations,
  };
}
