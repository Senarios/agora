import React, { useEffect } from "react";
import ReactDOM from "react-dom";
import "./fontawesome";
import { Route, Switch } from 'react-router-dom'
import "./Home";
import Index from '../pages/index'
import Meeting from '../pages/meeting'
import { BrowserRouterHook } from '../utils/use-router'
import { ContainerProvider } from '../utils/container'
import { ThemeProvider } from '@material-ui/styles'
import THEME from '../utils/theme'


function Example() {
  return (
    <ThemeProvider theme={THEME}>
      <ContainerProvider>
        <BrowserRouterHook>
          <Switch>
            <Route exact path="/meeting/:name" component={Meeting}></Route>
            <Route path="/" component={Index}></Route>
          </Switch>
        </BrowserRouterHook>
      </ContainerProvider >
    </ThemeProvider >

  );
}

export default Example;

if (document.getElementById("example")) {
  ReactDOM.render(
    // <ThemeProvider theme={THEME}>
    //   <ContainerProvider>
    <Example />
    //   {/* </ContainerProvider >
    // </ThemeProvider > */}

    , document.getElementById("example"));
}
