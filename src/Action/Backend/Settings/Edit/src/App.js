import React, { Component } from "react";

import "./App.css";
import Form from "react-jsonschema-form";

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      config: window.location.search.split("config=")[1],
      formData: {},
      uiSchema: {},
      jsonschema: {}
    };
    this.load();
  }

  load() {
    fetch("/admin/settings/" + this.state.config + "/data/schema").then(
      response => this.setState({ jsonschema: response })
    );

    fetch("/admin/settings/" + this.state.config + "/data/config").then(response =>
      this.setState({ formData: response })
    );

    fetch("/admin/settings/" + this.state.config + "/data/ui").then(
      response => this.setState({ uiSchema: response })
    );
  }
  render() {
    const log = type => console.log.bind(console, type);

    if (
      (this.state.jsonschema != {} && this.state.formData != {},
      this.state.uiSchema != {})
    ) {
      return (
        <Form
          schema={this.state.jsonschema}
          formData={this.state.formData}
          uiSchema={this.state.uiSchema}
          onChange={log("changed")}
          onSubmit={json => {
            (async () => {
              const rawResponse = await fetch(
                "/admin/settings/" + this.state.config,
                {
                  method: "POST",
                  headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json"
                  },
                  body: JSON.stringify(json)
                }
              );
              const content = await rawResponse.json();

              console.log(content);
            })();
          }}
          onError={log("errors")}
        />
      );
    }
    return null;
  }
}

export default App;
