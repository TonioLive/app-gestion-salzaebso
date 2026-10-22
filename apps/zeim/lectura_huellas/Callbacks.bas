Attribute VB_Name = "Callbacks"
'-------------------------------------------------------------------------------
'GrFinger Sample
'(c) 2005 Griaule Tecnologia Ltda.
'http://www.griaule.com
'-------------------------------------------------------------------------------
'
'This sample is provided with "GrFinger Fingerprint Recognition Library" and
'can't run without it. It's provided just as an example of using GrFinger
'Fingerprint Recognition Library and should not be used as basis for any
'commercial product.
'
'Griaule Tecnologia makes no representations concerning either the merchantability
'of this software or the suitability of this sample for any particular purpose.
'
'THIS SAMPLE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR
'IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
'OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
'IN NO EVENT SHALL GRIAULE BE LIABLE FOR ANY DIRECT, INDIRECT,
'INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
'NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
'DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
'THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
'(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
'THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
'
'You can download the free version of GrFinger directly from Griaule website.
'
'These notices must be retained in any copies of any part of this
'documentation and/or sample.
'
'-------------------------------------------------------------------------------

' -----------------------------------------------------------------------------------
' Callback routines
' -----------------------------------------------------------------------------------

Option Explicit
Option Base 0

' Global variables used to store data returned by
' an event. This data is used in consumer Subs.
Private myPIdSensor As Long
Private myEventRaised As Long
Private myWidth As Long
Private myHeight As Long
Private myRes As Long
Private myRawImage As Long

' Boolean variables that signs that an event was fired and
' must be consumed.
Public fireStatus As Boolean
Public fireFinger As Boolean
Public fireImage As Boolean

' Initialize capture library
Public Function InitializeGrCap() As Integer
    ' Initializing event signal variables to false.
    fireStatus = False
    fireImage = False
    fireFinger = False

    ' Initializing GrCapture. Passing adress of "GrCapStatusEventHandler" sub.
    InitializeGrCap = GrCapInitialize(AddressOf GrCapStatusEventHandler)
End Function

' Handler to status events. This sub is called evertime an status
' event is raised. Data from event is copied to global variables
' to be consumed later. Since Visual Basic 6 is not thread-safe, only simple
' operations such as asignements can be done inside this handler.
Public Sub GrCapStatusEventHandler(ByVal pidSensor As Long, ByVal eventRaised As Long)
    ' *** DO NOT ADD THREAD-UNSAFE CODE HERE! ***
    ' *** THIS SUB ONLY SIGNS AN EVENT! THE DATA MUST BE CONSUMED OUTSIDE! ***

    ' Waiting for the last event to be consumed before
    ' signal current event.
    While fireStatus = True
     DoEvents
    Wend

    ' Copying event data.
    myPIdSensor = pidSensor
    myEventRaised = eventRaised
    
    ' Signaling event.
    fireStatus = True

    ' Wait until event is consumed
    While fireStatus = True
     DoEvents
    Wend
End Sub

' Handler to finger events. This sub is called evertime an finger
' event is raised. Data from the event is copied to global variables
' to be consumed later. Since Visual Basic 6 is not thread-safe, only simple
' operations such as asignements can be done inside this handler.
Public Sub GrCapFingerEventHandler(ByVal pidSensor As Long, ByVal eventRaised As Long)
    ' *** DO NOT ADD THREAD-UNSAFE CODE HERE! ***
    ' *** THIS SUB ONLY SIGNS AN EVENT! THE DATA MUST BE CONSUMED OUTSIDE! ***

    ' Waiting for the last event to be consumed before
    ' signal current event.
    While fireFinger = True
     DoEvents
    Wend
    
    ' Copying event data.
    myPIdSensor = pidSensor
    myEventRaised = eventRaised
    
    ' Signaling event.
    fireFinger = True

    ' Wait until event is consumed
    While fireFinger = True
     DoEvents
    Wend
End Sub

' Handler to image events. This sub is called evertime an image
' event is raised. Data from event is copied to global variables
' to be consumed later. Since Visual Basic 6 is not thread-safe, only simple
' operations such as asignements can be done inside this handler.
Public Sub GrCapImageEventHandler(ByVal pidSensor As Long, ByVal width As Long, ByVal height As Long, ByVal pRawImage As Long, ByVal res As Long)
    ' *** DO NOT ADD THREAD-UNSAFE CODE HERE! ***
    ' *** THIS SUB ONLY SIGNS AN EVENT! THE DATA MUST BE CONSUMED OUTSIDE! ***
    
    ' Waiting for the last event to be consumed before
    ' signal current event.
    While fireImage = True
     DoEvents
    Wend
    
    ' Copying event data.
    myPIdSensor = pidSensor
    myWidth = width
    myHeight = height
    myRes = res
    myRawImage = pRawImage
    
    ' Signaling event.
    fireImage = True
    
    ' Wait until event is consumed
    While fireImage = True
     DoEvents
    Wend
End Sub

' Consumes data that came from an Status Event.
Public Sub ConsumeStatusEvent()
    Dim myIdSensor As String
    
    ' Convert C string to VB string to get sensor ID
    myIdSensor = ptrToStr(myPIdSensor)
    
    ' Signals that a status event ocurred.
    Call WriteEvent(myIdSensor, myEventRaised)
    
    ' Checking if event raised is a plug or unplug.
    If (myEventRaised = GR_PLUG) Then
        ' Start capturing from plugged sensor.
        GrCapStartCapture myIdSensor, AddressOf GrCapFingerEventHandler, AddressOf GrCapImageEventHandler
    Else
        ' Stop capturing from unplugged sensor.
        GrCapStopCapture (myIdSensor)
    End If
End Sub

' Consumes data that came from an Finger Event.
Public Sub ConsumeFingerEvent()
    Dim myIdSensor As String
    
    ' Convert C string to VB string to get sensor ID
    myIdSensor = ptrToStr(myPIdSensor)
            
    ' Just signals that a finger event ocurred.
    Call WriteEvent(myIdSensor, myEventRaised)
End Sub

' Consumes data that came from an Image Event.
Public Sub ConsumeImageEvent()
    Dim myIdSensor As String
    
    ' Convert C string to VB string to get sensor ID
    myIdSensor = ptrToStr(myPIdSensor)
    
    ' Copying aquired image
    With raw
        ' Image data must be copied using previously defined function
        ' that copies memory content. It is necessary because Visual Basic
        ' does not have native suporte to pointers, C++ and DLL arrays.
        .img = ptrToByteArr(myRawImage, myHeight * myWidth)
        .height = myHeight
        .width = myWidth
        .res = myRes
    End With
        
    ' Signaling that an Image Event occurred.
    Call WriteEvent(myIdSensor, GR_IMAGE)

    ' display fingerprint image
    Call PrintBiometricDisplay(False, GR_DEFAULT_CONTEXT)
    
    ' now we have a fingerprint, so we can extract template
    formMain.btEnroll.Enabled = False
    formMain.btExtract.Enabled = True
    formMain.btIdentify.Enabled = False
    formMain.btVerify.Enabled = False
    
    ' extracting template from image
    If formMain.ckBoxAutoExtract.Value Then Call formMain.btExtract_Click
    ' identify fingerprint
    If formMain.ckBoxAutoIdentify.Value Then Call formMain.btIdentify_Click
End Sub
