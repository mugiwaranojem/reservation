import React, { useState, useEffect } from 'react';
import axios from 'axios';
import {
  Container,
  Typography,
  TextField,
  Button,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  CircularProgress,
  Box,
} from '@mui/material';
import ReservationFormModal from './ReservationFormModal';
import AddIcon from '@mui/icons-material/Add';
import { toLocalTimezone } from '../utils/DateTimeHelper';

export default function ReservationsDashboard() {
  const [reservations, setReservations] = useState([]);
  const [pin, setPin] = useState('');
  const [confirmMessage, setConfirmMessage] = useState('');
  const [openModal, setOpenModal] = useState(false);
  const [loading, setLoading] = useState(true);
  const [confirming, setConfirming] = useState(false);

  useEffect(() => {
    fetchReservations();
  }, []);

  const fetchReservations = async () => {
    try {
      setLoading(true);
      const response = await axios.get('http://localhost:8000/api/reservations');
      setReservations(response.data.data);
      setLoading(false);
    } catch (error) {
      console.error('Error fetching reservations', error);
    }
  };

  const handleConfirm = async () => {
    setConfirming(true);
    await axios.post(`http://localhost:8000/api/reservations/${pin}/confirm`)
      .then(() => {
        setConfirmMessage('Reservation confirmed successfully.');
        setPin('');
        setConfirming(false);
        fetchReservations();
      })
      .catch(() => {
        setConfirmMessage('Confirmation failed. Please try again later.');
        setConfirming(false);
      });
  };

  const handleReservationCreated = () => {
    fetchReservations(); 
  };

  const formatDateTime = (utcDateTime) => {
    return toLocalTimezone(utcDateTime);
  };

  return (
    <Container sx={{ mt: 4 }}>
      <Typography variant="h4" gutterBottom>
        Reservations Dashboard
      </Typography>

      <Box display="flex" justifyContent="space-between" alignItems="center" mt={1}>
        <Box sx={{ mb: 4 }}>
          <Typography variant="h6">Confirm Reservation by PIN</Typography>
          <Box display="flex" gap={2} alignItems="center" mt={1}>
            <TextField
              label="Enter PIN Code"
              value={pin}
              onChange={e => setPin(e.target.value)}
              size="small"
            />
            <Button
              variant="contained"
              onClick={handleConfirm}
              disabled={confirming}
              color="success"
            >
                {confirming ? (
                  <>
                    <CircularProgress size={24} sx={{ color: 'white' }} /> 
                    Confirming...
                  </>
                ) : (
                  'Confirm'
                )}
            </Button>
          </Box>
          {confirmMessage && <Typography sx={{ mt: 1 }}>{confirmMessage}</Typography>}
        </Box>

        <Button
          variant="contained"
          onClick={() => setOpenModal(true)}
          sx={{ mb: 2 }}
          startIcon={<AddIcon />}  // Add the plus icon before the button text
        >
          Add Reservation
        </Button>
      </Box>


      <ReservationFormModal
        open={openModal}
        onClose={() => setOpenModal(false)}
        onReservationCreated={handleReservationCreated}
      />

      <TableContainer component={Paper} sx={{ mb: 12 }}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Time</TableCell>
              <TableCell>Name</TableCell>
              <TableCell>Phone</TableCell>
              <TableCell>Status</TableCell>
              <TableCell>Extension</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {loading ? (
              <TableRow>
                <TableCell colSpan={6} align="center">
                  <CircularProgress />
                </TableCell>
              </TableRow>
            ) : (
              reservations.map((reservation) => (
                <TableRow hover sx={{ '&:hover': { backgroundColor: '#f5f5f5' } }} key={reservation.id}>
                  <TableCell>{reservation.id}</TableCell>
                  <TableCell>{formatDateTime(reservation.reservation_time)}</TableCell>
                  <TableCell>{reservation.first_name} {reservation.last_name}</TableCell>
                  <TableCell>{reservation.phone_number}</TableCell>
                  <TableCell
                    sx={{
                      color:
                        reservation.status === 'pending'
                          ? 'orange'
                          : reservation.status === 'confirmed'
                          ? 'green'
                          : reservation.status === 'expired'
                          ? 'red'
                          : 'inherit',
                      fontWeight: 'bold',
                    }}
                  >
                    {reservation.status}
                  </TableCell>
                  <TableCell>{reservation.reservation_extension} min</TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </TableContainer>
    </Container>
  );
}
