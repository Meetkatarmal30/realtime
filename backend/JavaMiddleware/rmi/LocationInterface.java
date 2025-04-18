package rmi;

import java.rmi.Remote;
import java.rmi.RemoteException;

public interface LocationInterface extends Remote {
    String sendLocation(int tripId, double latitude, double longitude) throws RemoteException;
}
