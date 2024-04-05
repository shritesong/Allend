import {React}from 'react';
import { Modal, Button } from 'react-bootstrap';
import Cookies from 'js-cookie';
const CaseDetailsModal2 = ({ show, onHide ,number,data}) => {
  const submit = (cid) =>{
    fetch(`http://127.0.0.1/Allend/backend/public/api/take_submit?cid=${cid}`,{
      method: 'GET',
      headers: {
        Authorization: `Bearer ${Cookies.get('token')}`,
      },
    })
    .then((res)=>{
      return res.json();
    })
    .then((data)=>{
      
      console.log(data)
    })
  }
  return (
    <>
    {data.length === 0 
    ? " "
    :
    <Modal show={show} onHide={onHide} size="lg">
      <Modal.Header closeButton>
        <Modal.Title>案件資訊</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <div className="container" style={{ fontSize: '18px' }}>
          <div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>案件編號：{data[number].cid}</strong> 
            </div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>案件名稱：{data[number].c_name}</strong>
            </div>
          </div>
          <div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>案件類別：{data[number].type}</strong>
            </div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>案件地點：{data[number].active_location}</strong> 
            </div>
          </div>
          <div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>案件金額：</strong>{data[number].c_amount}/{data[number].c_unit}
            </div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>發案人姓名：{data[number].c_contact_name}</strong> 
            </div>
          </div>
          <div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>發案人Email：{data[number].c_email}</strong> 
            </div>
            <div className="col" style={{ marginBottom: '10px', fontSize: '20px' }}>
              <strong>發案人手機：{data[number].c_mobile_phone}</strong> 
            </div>
          </div>
        </div>
        <div className="d-grid gap-2">
          <Button variant="primary" size="lg">
            聯絡案主
          </Button>
          <Button variant="secondary" size="lg" onClick={()=>{submit(data[number].cid)}}>
            提交案件
          </Button>
        </div>
      </Modal.Body>
      <Modal.Footer>
        <Button variant="secondary" onClick={onHide}>
          關閉
        </Button>
      </Modal.Footer>
    </Modal>}
    </>
  );
};

export default CaseDetailsModal2;
