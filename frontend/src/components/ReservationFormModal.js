import React, { useState, useEffect } from 'react';
import {
  Modal, Box, Typography, TextField, Button, Alert, CircularProgress
} from '@mui/material';
import axios from 'axios';
import { currentDateTime, appendTimezoneOffset } from '../utils/DateTimeHelper';
import { Formik, Form } from 'formik';
import * as Yup from 'yup';

const style = {
  position: 'absolute',
  top: '50%',
  left: '50%',
  transform: 'translate(-50%, -50%)',
  width: 400,
  bgcolor: 'background.paper',
  boxShadow: 24,
  borderRadius: 2,
  p: 4,
};

const ReservationFormModal = ({ open, onClose, onReservationCreated }) => {
  const [alert, setAlert] = useState({ type: '', message: '', visible: false });
  const [loading, setLoading] = useState(false);

  const validationSchema = Yup.object({
    first_name: Yup.string().required('First name is required'),
    last_name: Yup.string().required('Last name is required'),
    phone_number: Yup.string()
      .matches(/^\+?\d{7,15}$/, 'Enter a valid phone number')
      .required('Phone number is required'),
    reservation_time: Yup.string().required('Reservation time is required'),
  });

  useEffect(() => {
    if (open) {
        setAlert({
            type: '',
            message: '',
            visible: false,
        });
    }
  }, [open]);

  const defaultTime = currentDateTime();

  const onSubmit = async (values, resetForm) => {
    try {
      setLoading(true);
      const response = await axios.post('http://localhost:8000/api/reservations', {
        reservation_time: appendTimezoneOffset(values.reservation_time),
        first_name: values.first_name,
        last_name: values.last_name,
        phone_number: values.phone_number,
      });
      setLoading(false);

      setAlert({
        type: 'success',
        message: `Reservation Created. PIN: ${response?.data?.data?.pin_code}`,
        visible: true,
      });
      resetForm();
      onReservationCreated();
    } catch (error) {
      const message =
        (error.response?.data?.errors && Object.values(error.response.data.errors).flat().join(', ')) ||
        'Error creating reservation';

      setAlert({
        type: 'error',
        message,
        visible: true,
      });

      setLoading(false);
    }
  };

  return (
    <Modal open={open} onClose={onClose}>
      <Box sx={style}>
        <Typography variant="h6" mb={2}>Create Reservation</Typography>

        {alert.visible && (
          <Alert severity={alert.type} sx={{ mb: 2 }}>
            {alert.message}
          </Alert>
        )}

        <Formik
          initialValues={{
            first_name: '',
            last_name: '',
            phone_number: '',
            reservation_time: defaultTime,
          }}
          validationSchema={validationSchema}
          onSubmit={(values, { resetForm }) => {
            onSubmit(values, resetForm);
          }}
        >
          {({ values, errors, touched, handleChange, handleBlur }) => (
            <Form>
              <TextField
                label="First Name"
                name="first_name"
                fullWidth
                margin="normal"
                value={values.first_name}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.first_name && Boolean(errors.first_name)}
                helperText={touched.first_name && errors.first_name}
              />

              <TextField
                label="Last Name"
                name="last_name"
                fullWidth
                margin="normal"
                value={values.last_name}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.last_name && Boolean(errors.last_name)}
                helperText={touched.last_name && errors.last_name}
              />

              <TextField
                label="Phone Number"
                name="phone_number"
                fullWidth
                margin="normal"
                value={values.phone_number}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.phone_number && Boolean(errors.phone_number)}
                helperText={touched.phone_number && errors.phone_number}
              />

              <TextField
                label="Reservation Time"
                name="reservation_time"
                type="datetime-local"
                fullWidth
                margin="normal"
                InputLabelProps={{ shrink: true }}
                value={values.reservation_time}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.reservation_time && Boolean(errors.reservation_time)}
                helperText={touched.reservation_time && errors.reservation_time}
              />

                <Button
                type="submit"
                variant="contained"
                fullWidth
                sx={{ mt: 2 }}
                disabled={loading}
                >
                {loading ? (
                    <CircularProgress size={24} sx={{ color: 'white' }} />
                ) : (
                    'Submit'
                )}
            </Button>
            </Form>
          )}
        </Formik>

    
      </Box>
    </Modal>
  );
};

export default ReservationFormModal;
