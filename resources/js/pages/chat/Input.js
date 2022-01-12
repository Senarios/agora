import React, { useEffect, useState, useMemo } from "react";
import { database } from "firebase";
import { emojis } from "./emoji";
import Input from "@material-ui/core/Input";


const InputTetx = (props) => {
    const [text, setText] = useState("");

    useEffect(() => {

    }, [])


    const sendMessage = (event) => {
        event.preventDefault();

        if (text.trim() !== "") {
            database().ref().child("streams/987654/chat").push().set({
                message: text,
            });
            setText("")
        }
    }
    const changeVal = (e) => {
        const abc = e.target.value
        setText(abc)
    }


    return (
        <div>
            <div>
                <form onSubmit={sendMessage} >
                    <Input type="text" onChange={changeVal} value={text} />
                </form>
            </div>
        </div>
    )

}

export default InputTetx;
