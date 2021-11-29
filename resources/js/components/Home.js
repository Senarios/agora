// import { Button } from "react-bootstrap";
// import { Modal } from "react-bootstrap";
// import React, { useState, useEffect, useRef } from "react";
// import "./Home.css";

// function Home(props) {
  
//   return (
//     <>
//     <h1>hello</h1>
//     </>
//     )}
     
// export default Home;


import React, { useEffect } from 'react'
import { useGlobalState, useGlobalMutation } from "../utils/container"
import { makeStyles } from '@material-ui/core/styles'
import { Container } from '@material-ui/core'
import IndexCard from '../pages/index/index-card'

const useStyles = makeStyles(() => ({
  container: {
    height: '100%',
    width: '100%',
    minWidth: 800,
    minHeight: 600,
    boxSizing: 'content-box',
    display: 'flex',
    justifyContent: 'center'
  }
}))

const Index = () => {
  const stateCtx = useGlobalState()
  const mutationCtx = useGlobalMutation()
  const classes = useStyles()

  useEffect(() => {
    if (stateCtx.loading === true) {
      mutationCtx.stopLoading()
    }
  }, [stateCtx.loading, mutationCtx])

  return (
    <Container maxWidth="sm" className={classes.container}>
      <IndexCard />
      {/* <h1>hhhh</h1> */}
    </Container>
  )
}

export default Index
