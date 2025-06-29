import React from 'react';
import '../Style/Modal.css';
import { useFormik } from 'formik';
import * as Yup from 'yup';

const AddressForm = ({ onClose, onSave, initialValues }) => {
  const formik = useFormik({
    initialValues: initialValues || {
    
      address: {
        city: '',
        street_address: '',
        floor: '',
        district: '',
      }
    },
    enableReinitialize: true,

    validationSchema: Yup.object({

      address: Yup.object({
        city: Yup.string().required('Required'),
        street_address: Yup.string().required('Required'),
        floor: Yup.string(),
        district: Yup.string().required('Required'),

      }),
    }),
    onSubmit: (values) => {
      onSave(values);
    }
  });

  return (
    <div className="modal-overlay">
      <div className="modal-content">
        <h3>Add New Address</h3>
        <form onSubmit={formik.handleSubmit} className="modal-form">

          {/* Address Detail */}
          <div className="row">
            <label htmlFor="address.city">City</label>
            <select id="address.city" {...formik.getFieldProps('address.city')}>
              <option value="">Select City</option>
              <option value="Cairo">Cairo</option>
              <option value="Giza">Giza</option>
              <option value="Alexandria">Alexandria</option>
              <option value="Mansoura">Mansoura</option>
              <option value="Tanta">Tanta</option>
            </select>
            {formik.touched.address?.city && formik.errors.address?.city && (
              <div className="error">{formik.errors.address.city}</div>
            )}
          </div>

          <div className="row">
            <label htmlFor="address.district">District</label>
            <input
              id="address.district"
              type="text"
              {...formik.getFieldProps('address.district')}
            />
            {formik.touched.address?.district && formik.errors.address?.district && (
              <div className="error">{formik.errors.address.district}</div>
            )}
          </div>



          <div className="row">
            <label htmlFor="address.street_address">Street Address</label>
            <input id="address.street_address" type="text" {...formik.getFieldProps('address.street_address')} />
            {formik.touched.address?.street_address && formik.errors.address?.street_address && (
              <div className="error">{formik.errors.address.street_address}</div>
            )}
          </div>


          <div className="form-actions">
            <button type="button" onClick={onClose} className="cancel-btn">Cancel</button>
            <button type="submit" className="save-btn">Save</button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default AddressForm;
