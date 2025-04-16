# Clean previous .class files
Remove-Item rmi\*.class -Force

# Compile using JDK 24
& "C:\Program Files\Java\jdk-24\bin\javac.exe" rmi\*.java

# Start rmiregistry
Start-Process -NoNewWindow -FilePath "C:\Program Files\Java\jdk-24\bin\rmiregistry.exe"

# Run server
& "C:\Program Files\Java\jdk-24\bin\java.exe" rmi.LocationServer
