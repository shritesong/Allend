import React, { useState, useEffect, useContext } from "react";
import { Modal, Button, Form, Row, Col } from "react-bootstrap";
import Cookies from "js-cookie";
import { CaseContext } from "./MainScreen3";
import { MediaContext } from "./Media";


const CaseDetailsModal3 = ({ show, onHide }) => {
  //useContext from Mainscreen
  // const {setSelectedItems, setCheckedAll} = useContext(MediaContext) ;
  const { fetchData } = useContext(CaseContext);

  const [nameOfVideo, setNameOfVideo] = useState("");
  const [details, setDetails] = useState("");
  const [URL, setURL] = useState("");


  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData();
    formData.append('v_name', nameOfVideo);
    formData.append('v_description', details);
    formData.append('src', URL);


    await fetch('http://127.0.0.1/Allend/backend/public/api/video', {
      method: 'POST',
      headers: {
        
        Authorization: `Bearer ${Cookies.get('token')}`,
      },
      body: formData,
    })
      .then((res) => {
        console.log(res);
        onHide();
        fetchData();
        // setSelectedItems(Array(6).fill(false));
        // setCheckedAll(false);
      })
      .catch((error) => {
        console.error('There was a problem updating the case:', error);
      });
  };

  return (
    <Modal
      show={show}
      onHide={onHide}
      className="row justify-content-center w-100"
    >
      <Modal.Header closeButton>
        <Modal.Title>案件資訊</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <Form >
          <Form.Group controlId="numberOfPeople">
            <Form.Label>影片名稱：</Form.Label>
            <Form.Control
              type="text"
              placeholder="填寫影片名稱"
              value={nameOfVideo}
              onChange={(e) => setNameOfVideo(e.target.value)}
              required
            />
          </Form.Group>
          <Form.Group controlId="details">
            <Form.Label>影片描述：</Form.Label>
            <Form.Control
              as="textarea"
              placeholder=""
              value={details}
              onChange={(e) => setDetails(e.target.value)}
              required
            />
          </Form.Group>
          <Form.Group controlId="formImageFile">
            <Form.Label>上傳連結</Form.Label>
            <Form.Control type="url" value={URL} onChange={(e) => setURL(e.target.value)} />
          </Form.Group>

        </Form>
        <div className="mb-2 d-flex justify-content-around">
          <Button
            variant="primary"
            size="lg"
            onClick={(e) => {
              e.preventDefault(); // Prevent default form submission
              handleSubmit(e); // Pass the event object to handleSubmit
            }}
            style={{ padding: '0.5rem 2.14rem', fontSize: '22px', borderRadius: "10px" }}
          >
            發佈
          </Button>
          <Button variant="secondary" size="lg" onClick={onHide} style={{ padding: '0.5rem 2.14rem', fontSize: '22px', borderRadius: "10px" }}>
            取消
          </Button>
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default CaseDetailsModal3;
