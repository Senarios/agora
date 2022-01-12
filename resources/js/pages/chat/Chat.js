import React, { useEffect, useState, useMemo } from "react";
import { database } from "firebase";
import Input from './Input'

const Chat = (props) => {
    const [streamMesg, setStreamMesg] = useState([]);
    const [noViews, setNoViews] = useState([]);
    const [roomname] = useState("streams/987654/chat");

    console.log("===>voewew===>", noViews)

    useEffect(() => {
        hello();
        viwes();
    }, [])

    const hello = async () => {
        let rootRef = await database().ref().child(roomname);
        await rootRef.on('value', snapshot => {
            let messages = [];
            snapshot.forEach(snap => {
                messages.push({
                    message: snap.val().message,
                    key: snap.key,
                    type: snap.val().type
                });
            });
            setStreamMesg(messages)

        });
    }

    const viwes = async () => {
        await database().ref().child("streams/987654").child("viewers").on("value", snap => {
            let newMsg = [];

            newMsg.push({
                viewers: snap.val(),
            });

            setNoViews(newMsg)
        })
    }

    return (
        <div>
            <div>
                <div style={{ marginBottom: 200 }}>
                    <div className="default-avatar_view"></div>
                    {
                        noViews && noViews.map((conversation, i) => (
                            <p style={{ fontSize: 16, color: "#fff", marginLeft: 35, marginTop: -25 }}>{conversation.viewers}</p>
                        ))
                    }
                </div>
                <div style={{ height: 400 }}>
                    {
                        streamMesg && streamMesg.map((conversation, i) => (
                            <p style={{ fontSize: 14, color: "#fff" }}>{conversation.message}</p>
                        ))
                    }
                </div>
            </div>


        </div>

    )

}

export default Chat;
